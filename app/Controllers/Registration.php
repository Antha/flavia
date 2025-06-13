<?php

namespace App\Controllers;
use App\Models\UserModel;

class Registration extends BaseController
{
    protected $session_user;

    public function index(): string
    {
        return view('registration_page');
    }

    public function __construct()
    {
        $this->session_user = session();
    } 

    public function success(): string
    {
        return view('registration_success_page');
    }

    public function auth()
    {
        $validation = \Config\Services::validation();

        // Aturan validasi
        $rules = [
            'fl_name' => 'required',
            'username' => [
                'label' => 'Username',
                'rules' => 'required|alpha_numeric|is_unique[users.username]|min_length[3]|max_length[20]',
                'errors' => [
                    'required' => 'Username wajib diisi.',
                    'alpha_numeric' => 'Username hanya boleh mengandung huruf dan angka.',
                    'is_unique' => 'Username sudah digunakan.',
                    'min_length' => 'Username minimal 3 karakter.',
                    'max_length' => 'Username maksimal 20 karakter.'
                ]
            ],
            'password' => 'required|min_length[6]',
            'confirm_password' => 'matches[password]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'outlet_name' => 'required',
            'digipos_id' => 'required',
            'link_aja' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/registration')->withInput()->with('errors', $validation->getErrors());
            //return $this->response->setJSON(["error"=>"not valid"]);
        }

        $userModel = new UserModel();
        $token = bin2hex(random_bytes(32)); // Generate token unik

        //$idcard = $this->saveBase64Image($this->request->getPost("imageData"), "./uploads/idcard");

        //$idcard = 0;
        // if( $idcard === false || $idcard == 0){
        //     return redirect()->to('/registration')->withInput()->with('error_image', 'Gagal mengupload gambar, silakan coba lagi.');
        // }

        $data = [
            'fl_name' => $this->request->getPost('fl_name'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'email' => $this->request->getPost('email'),
            'outlet_name' => $this->request->getPost('outlet_name'),
            'digipos_id' => $this->request->getPost('digipos_id'),
            'level' => 'user',
            'idcard' => "",
            'token' => $token,
            'link_aja' => "62".$this->request->getPost('link_aja'),
            'status' => 0 // Belum aktif
            
        ];

        $userModel->save($data);

        // Kirim email konfirmasi
        $this->sendVerificationEmail($data['email'], $token);

        return redirect()->to('/registration/success');
    }

    private function sendVerificationEmail($email, $token)
    {
        
        $emailService = \Config\Services::email();
        $emailService->setFrom('admin@salesbalnus.com', 'Admin Sales Balnus');
        $emailService->setTo($email);
        $emailService->setSubject('Account Verification');
        $emailService->setMessage('Click the link to verify your account: ' . base_url('/registration/verify/' . $token));
        $emailService->send();
    }

    public function verify($token)
    {
        $userModel = new UserModel();
        $user = $userModel->where('token', $token)->first();

        if ($user) {
            $userModel->update($user['id'], ['status' => 1, 'token' => null]);
            return redirect()->to('/login')->with('message', 'Account verified! You can now login.');
        } else {
            return redirect()->to('/registration')->with('error', 'Invalid token!');
        }
    }

    function update(){
        $userModel = new UserModel();

        $validation = \Config\Services::validation();

        // Aturan validasi
        $rules = [
            'outlet_name' => [
                'rules'  => 'required|trim|not_space_only',
                'errors' => [
                    'required' => 'Nama Outlet wajib diisi!',
                    'not_space_only' => 'Nama Outlet tidak boleh hanya berisi spasi!'
                ]
            ],
            'link_aja' => [
                'rules'  => 'required|trim|not_space_only',
                'errors' => [
                    'required' => 'Link Aja wajib diisi!',
                    'not_space_only' => 'Link Aja tidak boleh hanya berisi spasi!'
                ]
            ],
            'digipos_id' => [
                'rules'  => 'required|trim|not_space_only|min_length[10]',
                'errors' => [
                    'required' => 'ID Digipos wajib diisi!',
                    'not_space_only' => 'ID Digipos tidak boleh hanya berisi spasi!',
                    'min_length' => 'ID Digipos harus terdiri dari minimal 10 digit!'
                ]
            ],
            'imageData' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Anda Harus Photo Dulu!'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/home')->withInput()->with('errors', $validation->getErrors());
            //return $this->response->setJSON(["error"=>"not valid"]);
        }
        
        $id = $this->request->getPost('id'); // Pastikan ID dikirim dari form

        $idcard = $this->saveBase64Image($this->request->getPost("imageData"), "./uploads/idcard");

        if( $idcard === false || $idcard == 0){
            return redirect()->to('/home')->withInput()->with('error_image', 'Gagal mengupload gambar, silakan coba lagi.');
        }

        $data = [
            'outlet_name' => $this->request->getPost('outlet_name'),
            'digipos_id' => $this->request->getPost('digipos_id'),
            'link_aja' => "62" . $this->request->getPost('link_aja'),
            'idcard' => $idcard
        ];

        $userModel->update($id, $data);

        // Ambil data terbaru dari database
        $updatedData = $userModel->find($id);

        $this->session_user->set([
            'user_id' => $updatedData['id'],
            'idcard' => $updatedData['idcard'],
            'fl_name' => $updatedData['fl_name'],
            'username' => $updatedData['username'],
            'user_level' => $updatedData["level"],
            'outlet_name' => $updatedData["outlet_name"],
            'link_aja' => $updatedData["link_aja"],
            'digipos_id' => $updatedData["digipos_id"],
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/home')->withInput()->with('success_message', 'Berhasil Memperbaharui Data');
    }

    function saveBase64Image($base64String, $uploadPath) {
        // Cek apakah data Base64 valid
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1); // Hapus prefix Base64
            $type = strtolower($type[1]); // Dapatkan tipe file (png, jpg, jpeg, dll)

            // Pastikan tipe file valid
            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                return false;
            }

            $base64String = base64_decode($base64String); // Decode Base64
            if ($base64String === false) {
                return false;
            }

            // Buat nama file unik
            $fileName = uniqid() . '.' . $type;

            // Simpan file ke folder upload
            $filePath = rtrim($uploadPath, '/') . '/' . $fileName;
            if (file_put_contents($filePath, $base64String)) {
                return $fileName; // Return nama file
            }
        }

        return false; // Jika gagal
    }
}

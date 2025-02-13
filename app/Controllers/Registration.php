<?php

namespace App\Controllers;
use App\Models\UserModel;

class Registration extends BaseController
{
    public function index(): string
    {
        return view('registration_page');
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
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'matches[password]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'outlet_name' => 'required',
            'digipos_id' => 'required',
            'imageData' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Anda Harus Photo Dulu!'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/registration')->withInput()->with('errors', $validation->getErrors());
            //return $this->response->setJSON(["error"=>"not valid"]);
        }

        $userModel = new UserModel();
        $token = bin2hex(random_bytes(32)); // Generate token unik

        $idcard = $this->saveBase64Image($this->request->getPost("imageData"), "./uploads/idcard");

        $data = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'email' => $this->request->getPost('email'),
            'outlet_name' => $this->request->getPost('outlate_name'),
            'digipos_id' => $this->request->getPost('digipos_id'),
            'branch' => $this->request->getPost('branch_option'),
            'cluster' => $this->request->getPost('cluster_option'),
            'city' => $this->request->getPost('city_option'),
            'level' => 'user',
            'idcard' => $idcard,
            'token' => $token,
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
            return redirect()->to('/register')->with('error', 'Invalid token!');
        }
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

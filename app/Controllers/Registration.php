<?php

namespace App\Controllers;
use App\Models\UserModel;

class Registration extends BaseController
{
    public function index(): string
    {
        return view('registration_page');
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
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userModel = new UserModel();
        $token = bin2hex(random_bytes(32)); // Generate token unik

        $data = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'email' => $this->request->getPost('email'),
            'branch' => $this->request->getPost('branch'),
            'cluster' => $this->request->getPost('cluster'),
            'city' => $this->request->getPost('city'),
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
        $emailService->setFrom('your@email.com', 'Your App');
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
}

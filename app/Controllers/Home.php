<?php

namespace App\Controllers;

use CodeIgniter\Email\Email;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home_page');
    }

    public function sendEmail()
    {
        $email = \Config\Services::email();

        $to = $this->request->getPost('to');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');

        if (!$to || !$subject || !$message) {
            return $this->response->setJSON(['message'=>'Semua field harus diisi.'], 400);
        }

        $email->setFrom('admin@salesbalnus.com', 'Admin Sales Balnus');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) {
            return $this->response->setJSON(['message' => 'Email berhasil dikirim.'], 200);
        } else {
            // Menampilkan detail error
            $error = $email->printDebugger(['headers']);
            return $this->response->setJSON(['error' => 'Gagal mengirim email.', 'details' => $error], 500);
        }
    }
}
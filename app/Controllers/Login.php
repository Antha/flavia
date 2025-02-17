<?php

namespace App\Controllers;
!defined('BASEPATH') OR exit('No direct script access aloowed');

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserHistoriesModel;

helper('form');

class Login extends BaseController
{
    protected $user_model;
    protected $userHistoriesModel;
    protected $session_user;

    public function __construct()
    {
        $this->user_model = new UserModel();
        $this->userHistoriesModel = new UserHistoriesModel();
        $this->session_user = session();
    } 

    public function index()
    {
        if($this->session_user->get('isLogin') == TRUE) 
		{ 
			return redirect()->to(base_url('/home')); 
		}
		else {
            return view('login_page');
        }
    }

    public function authentication()
    {
        helper(['form']);
        
        //$ip = $this->request->getIPAddress();
        //$attemptsKey = "login_attempts_" . $ip;
        //$lockoutKey = "lockout_time_" . $ip;

        // Cek apakah masih dalam masa blokir
        //if ($this->session_user->get($lockoutKey) && time() - $this->session_user->get($lockoutKey) < 60) {
        //    return redirect()->back()->with('error', 'Anda diblokir sementara. Coba lagi dalam beberapa menit.');
        //}

        //ambil username dan password input
        $get_username = strtolower(trim($this->request->getPost('username'))); // Bersihkan input
        $get_password = trim($this->request->getPost('password'));

        // Cek apakah username ada di database
        $isUserExists = $this->user_model->where('LOWER(username)', $get_username)->first();
        
        if (!$isUserExists) {
            $this->session_user->setFlashdata('error', 'User not found');
            return redirect()->to('/login');
        }

        // Debugging: Pastikan password hash tersedia
        if (!isset($isUserExists['password']) || empty($isUserExists['password'])) {
            $this->session_user->setFlashdata('error', 'Password hash not found in database');
            return redirect()->to('/login');
        }

        // Cek password
        if (!password_verify($get_password, $isUserExists['password'])) {
            // Tambah jumlah percobaan login
            //$attempts = $this->session_user->get($attemptsKey) ?? 0;
            //dd($attempts);
            //$this->session_user->set($attemptsKey, $attempts + 1);
            $this->session_user->setFlashdata('error', 'Invalid Username or Password');
            return redirect()->to('/login');
        }else{
            //$this->session_user->remove([$attemptsKey, $lockoutKey]);

            // Simpan session jika login sukses
            $this->session_user->set([
                'user_id' => $isUserExists['id'],
                'idcard' => $isUserExists['idcard'],
                'username' => $isUserExists['username'],
                'user_level' => $isUserExists["level"],
                'outlet_name' => $isUserExists["outlet_name"],
                'link_aja' => $isUserExists["link_aja"],
                'digipos_id' => $isUserExists["digipos_id"],
                'isLoggedIn' => true,
            ]);

            // Insert ke log history
            $this->userHistoriesModel->insert([
                "id_user" => $isUserExists['id']
            ]);

            return redirect()->to('/home');
        }
    }

    public function logout(){
        $this->session_user->destroy();
        return redirect()->to(base_url('/login'));
    }
}
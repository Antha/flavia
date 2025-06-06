<?php

namespace App\Controllers;
!defined('BASEPATH') OR exit('No direct script access aloowed');

use App\Controllers\BaseController;
use App\Models\OutletModel;
use App\Models\UserModel;
use App\Models\UserHistoriesModel;

helper('form');

class Login extends BaseController
{
    protected $user_model;
    protected $outlet_model;
    protected $userHistoriesModel;
    protected $session_user;

    public function __construct()
    {
        $this->user_model = new UserModel();
        $this->outlet_model = new OutletModel();
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

         // Cek apakah user sudah aktif
         $isUserActive = $this->user_model->where('LOWER(username)', $get_username)->where('status','1')->first();
        
         if (!$isUserActive) {
             $this->session_user->setFlashdata('error', 'User anda belum aktif. Klik link pada email untuk aktivasi');
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
                'fl_name' => $isUserExists['fl_name'],
                'username' => $isUserExists['username'],
                'user_level' => $isUserExists["level"],
                'outlet_name' => $isUserExists["outlet_name"],
                'link_aja' => $isUserExists["link_aja"],
                'digipos_id' => $isUserExists["digipos_id"],
                'isLoggedIn' => true,
            ]);

            //$this->write_custom_log("level of user : ". $isUserExists["level"]);

            $outlet_get = $this->outlet_model
            ->where('id_outlet', strtolower($isUserExists["digipos_id"]))
            ->first();
        
            if ($outlet_get) {
                $this->session_user->set('region', $outlet_get['Regional']);
                $this->session_user->set('branch', $outlet_get['Branch']);
                $this->session_user->set('cluster', $outlet_get['Cluster']);
                $this->session_user->set('city', $outlet_get['Kabupaten']);
            } else {
                //$this->write_custom_log('DEALER_CODE not found in outlet_get');
            }
            
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

    // function write_custom_log($message, $filename = 'custom-log.txt')
    // {
    //     $filePath = WRITEPATH . 'logs/' . $filename;
    //     $time = date('Y-m-d H:i:s');
    //     $log = "[{$time}] {$message}" . PHP_EOL;

    //     file_put_contents($filePath, $log, FILE_APPEND);
    // }
}
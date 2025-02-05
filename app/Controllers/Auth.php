<?php

namespace App\Controllers;
!defined('BASEPATH') OR exit('No direct script access aloowed');

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserHistoriesModel;

helper('form');

class Auth extends BaseController
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
            return redirect()->to(base_url('/auth/login')); 
        }
    }

    public function login()
    {
        if($this->session_user->get('isLogin') == TRUE) 
		{ 
			return redirect()->to(base_url('/home')); 
		}
		else {
            return view('login_page'); 
        }
    }

    public function cekLogin()
    {
        $get_username = strtolower($this->request->getPost('username'));
        $get_password = $this->request->getPost('password');

        //cek if user exist
        $isUserExists = $this->user_model->where('username', $get_username)->first();

        if($isUserExists){
            if (password_verify($get_password, $isUserExists['password'])) {
                $this->session_user->set([
                    'user_id' => $isUserExists['id'],
                    'username' => $isUserExists['username'],
                    'user_level' => $isUserExists["level"],
                    'branch' => $isUserExists['branch'],
                    'cluster' => $isUserExists['cluster'],
                    'city' => $isUserExists['city'],
                    'isLoggedIn' => true,
                ]);
                
                //insert to log history
                $this->userHistoriesModel->insert([
                    "id_user" => $isUserExists['id']
                ]);
                
                return redirect()->to('/home');
            }
            else {
                $this->session_user->setFlashdata('error', 'Invalid Username or Password');
                return redirect()->to('/auth/login');
            }
        }else {
            $this->session_user->setFlashdata('error', 'User not found');
            return redirect()->to('/auth/login');
        }
    }

    public function logout(){
        $this->session_user->destroy();
        return redirect()->to(base_url('/auth/login'));
    }
}
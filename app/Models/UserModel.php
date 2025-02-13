<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'email', 'outlet_name', 'digipos_id', 'level', 'branch', 'cluster', 'city', 'token', 'idcard', 'status'];
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fl_name','username', 'password', 'email', 'outlet_name', 'link_aja', 'digipos_id', 'level', 'token', 'idcard', 'status'];
}

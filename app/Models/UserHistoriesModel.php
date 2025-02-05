<?php

namespace App\Models;

use CodeIgniter\Model;

class UserHistoriesModel extends Model
{
    protected $table = 'user_histories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','id_user', 'last_login'];
}
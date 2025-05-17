<?php

namespace App\Models;

use CodeIgniter\Model;

class OutletModel extends Model
{
    protected $table = 'outlet_reference_jateng';
    protected $primaryKey = 'id_outlet';
    protected $allowedFields = [];
}
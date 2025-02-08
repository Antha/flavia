<?php

namespace App\Models;

use CodeIgniter\Model;

class ScanHistoriesModel extends Model
{
    protected $table = 'scan_histories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['datetime', 'msisdn', 'status','user_id'];
    
    public function getOneByMsisdn($msisdn)
    {
        return $this->where('msisdn', $msisdn)->first();
    }

}

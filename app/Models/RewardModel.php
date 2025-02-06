<?php

namespace App\Models;

use CodeIgniter\Model;

class RewardModel extends Model
{
    protected $table = 'reward_item';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item', 'point', 'branch', 'cluster', 'periode'];

    function getRewardUpdateDate(){
        $result = $this->selectMax('periode')->first();

        return $result ? $result['periode'] : null;
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class ScanHistoriesModel extends Model
{
    protected $table = 'scan_histories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['datetime', 'msisdn', 'status','user_id','card_type'];
    
    public function getOneByMsisdn($msisdn)
    {
        return $this->where('msisdn', $msisdn)->first();
    }

    function isTableExists($periode){
        $db = \Config\Database::connect();

        if ($db->tableExists('sellout_barcode_'.$periode)) {
            $result = "1";
        } else {
            $result = "0";
        }

        return $result;
    }

    function getMaxUpdateDateFull(){
        $db = \Config\Database::connect();
        $builder = $db->table('scan_histories');
        return $builder->selectMax('datetime')->get()->getResultArray();
    }

    function getScanHistoryData($periode,$periodeDb,$user_id){
        $db = \Config\Database::connect();

        // Subquery A: scan_histories
        $subQueryA = $db->table('scan_histories')
            ->select('user_id, msisdn, `datetime` AS scan_date, card_type')
            ->like('datetime', $periode, 'after') // Correct LIKE usage
            ->where('user_id', $user_id);

        // Subquery B: sellout_barcode_202502
        $subQueryB = $db->table('sellout_barcode_'.$periodeDb)
            ->select('msisdn, star_status');

        // Main Query: LEFT JOIN A and B
        $builder = $db->table("({$subQueryA->getCompiledSelect(false)}) A")
            ->select("
                A.user_id, 
                A.scan_date, 
                A.msisdn, 
                A.card_type, 
                CASE WHEN B.star_status = 'PAYLOAD' THEN 'VALID' ELSE 'NOT VALID' END AS status_data, 
                CASE WHEN B.star_status = 'PAYLOAD' THEN '1000' ELSE '0' END AS POINT
            ", false)
            ->join("({$subQueryB->getCompiledSelect(false)}) B", 'A.msisdn = B.msisdn', 'left')
            ->orderBy('A.scan_date', 'DESC');

        return $builder->get()->getResultArray();
    }

    function getScanTotalOnly($periode,$periodeDb,$user_id){
        $db = \Config\Database::connect();

        // Subquery A: scan_histories
        $subQueryA = $db->table('scan_histories')
            ->select('user_id, msisdn, `datetime` AS scan_date, card_type')
            ->like('datetime', $periode, 'after')
            ->where('user_id', $user_id);

        // Subquery B: sellout_barcode_XXXX
        $subQueryB = $db->table('sellout_barcode_' . $periodeDb)
            ->select('msisdn, star_status');

        // Main Query: LEFT JOIN A and B
        $builder = $db->table("({$subQueryA->getCompiledSelect(false)}) A")
            ->select("
                COUNT(CASE WHEN A.card_type = 'byu' THEN A.msisdn END) AS total_byu,
                COUNT(CASE WHEN A.card_type = 'perdana' THEN A.msisdn END) AS total_perdana,
                COUNT(CASE WHEN A.card_type = 'byu' THEN A.msisdn END) + COUNT(CASE WHEN A.card_type = 'perdana' THEN A.msisdn END) AS total_card,
                (COUNT(CASE WHEN A.card_type = 'byu' AND B.star_status = 'PAYLOAD' THEN A.msisdn END) + COUNT(CASE WHEN A.card_type = 'perdana' AND B.star_status = 'PAYLOAD' THEN A.msisdn END))*1000 AS total_point
            ", false) // ✅ Counting msisdn for 'byu' and 'perdana'
            ->join("({$subQueryB->getCompiledSelect(false)}) B", 'A.msisdn = B.msisdn', 'left');

        return $builder->get()->getRowArray(); // ✅ Use getRowArray() since it's a single result

    }

    function getScanHistoryDataAdmin($periode,$periodeDb,$branch,$cluster){
        $db = \Config\Database::connect();

        
        // Subquery A: scan_histories
        $subQueryA = $db->table('scan_histories')
                ->select('user_id, msisdn, `datetime` AS scan_date, card_type')
                ->like('datetime', $periode, 'after'); // Correct LIKE usage

        // Subquery B: sellout_barcode_202502
        $subQueryB = $db->table('sellout_barcode_'.$periodeDb)
            ->select('msisdn, star_status');

        // Subquery C: users
        $subQueryC = $db->table('users')
            ->select('id, username, branch, cluster');
            
        /*if ($branch !== 'ALL' && !empty($cluster)) {
            $subQueryC->where('branch', $branch)
                        ->where('cluster', $cluster);
        }else if($branch !== 'ALL' && empty($cluster)){
            $subQueryC->where('branch', $branch);
        }*/

        // Main Query: LEFT JOIN A and B an C
        $builder = $db->table("({$subQueryA->getCompiledSelect(false)}) A")
            ->select("
                A.user_id, 
                A.scan_date,
                C.username,
                C.branch,
                C.cluster, 
                A.msisdn, 
                A.card_type, 
                CASE WHEN B.star_status = 'PAYLOAD' THEN 'VALID' ELSE 'NOT VALID' END AS status_data, 
                CASE WHEN B.star_status = 'PAYLOAD' THEN '1000' ELSE '0' END AS POINT
            ", false)
            ->join("({$subQueryB->getCompiledSelect(false)}) B", 'A.msisdn = B.msisdn', 'left') // ✅ Left join with B
            ->join("({$subQueryC->getCompiledSelect(false)}) C", 'A.user_id = C.id', 'left'); // ✅ Left join with C (users)
                // ✅ Dynamically add WHERE conditions outside the method chain
                if ($branch !== 'ALL' && !empty($cluster)) {
                    $builder->where('C.branch', $branch)
                            ->where('C.cluster', $cluster);
                } elseif ($branch !== 'ALL' && empty($cluster)) {
                    $builder->where('C.branch', $branch);
                }
            $builder->orderBy('A.scan_date', 'DESC');

        return $builder->get()->getResultArray();
    }

}

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

    function getLattestSoBarcodeTable(){
        $db = \Config\Database::connect();
        $targetDatabase = 'flavia'; // Ganti dengan nama database yang sesuai

        $builder = $db->table('INFORMATION_SCHEMA.TABLES')
            ->select('TABLE_NAME')
            ->where('TABLE_SCHEMA', $targetDatabase) // Filter berdasarkan database
            ->like('TABLE_NAME', 'sellout_barcode_%', 'after'); // Filter tabel yang diawali 'sellout_barcode_'

        $query = $builder->get();
        $results = $query->getRowArray(); // Mengambil hasil sebagai array

        return $results;
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

    function getScanHistoryDataAdmin($periodeDb){
        $db = \Config\Database::connect();
       
        $tableName = "sellout_barcode_{$periodeDb}"; // Nama tabel dinamis

        // Subquery A: users dengan status = '1'
        $subQueryA = $db->table('users')
            ->select('id, username, fl_name, outlet_name, digipos_id')
            ->where('status', '1');

        // Subquery D: Perhitungan so_byu_valid & so_perdana_valid
        $subQueryD = $db->table('scan_histories B')
            ->select("
                user_id, 
                COUNT(CASE WHEN LOWER(star_status) = 'payload' AND LOWER(card_type) = 'byu' THEN B.msisdn END) AS so_byu_valid,
                COUNT(CASE WHEN LOWER(star_status) != 'payload' AND LOWER(card_type) = 'perdana' THEN B.msisdn END) AS so_perdana_valid
            ", false)
            ->join("$tableName C", "CONCAT('0', SUBSTRING(B.msisdn, 3)) = C.msisdn", 'inner')
            ->groupBy('user_id');

        // Query utama dengan JOIN subqueries
        $builder = $db->table("({$subQueryA->getCompiledSelect(false)}) A")
            ->select("
                IFNULL(A.fl_name,'NULL') fl_name, 
                IFNULL(A.outlet_name,'NULL') outlet_name, 
                IFNULL(A.digipos_id,'NULL') digipos_id, 
                IFNULL(D.so_byu_valid,0) so_byu_valid, 
                IFNULL(D.so_perdana_valid,0) so_perdana_valid, 
                (IFNULL(D.so_byu_valid,0) + IFNULL(D.so_perdana_valid,0)) AS so_total
            ", false)
            ->join("({$subQueryD->getCompiledSelect(false)}) D", 'A.id = D.user_id', 'left')
            ->orderBy('so_total','DESC');

        $query = $builder->get();
        $results = $query->getResultArray();

        return $results;
    }

}

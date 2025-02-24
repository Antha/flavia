<?php

namespace App\Models;

use CodeIgniter\Model;

class ScanHistoriesModel extends Model
{
    protected $table = 'scan_histories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fl_name','digipos_id','outlet_name','datetime', 'msisdn', 'status','user_id','card_type'];
    
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

    function getScanHistoryDataDetail($periode,$periodeDb,$user_id){
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

    function getScanHistorySummaryUser($periodeDb,$user_id,$username,$outlet_name,$digipos_id){
        $db = \Config\Database::connect();

        // Validasi nama tabel agar tidak dieksploitasi (hindari SQL injection)
        if (!preg_match('/^\d{6}$/', $periodeDb)) {
            throw new \Exception("Format periode tidak valid");
        }
        $tableName = "sellout_barcode_" . $periodeDb; // Nama tabel yang valid

        $builder = $db->table('scan_histories B');
        $builder->select([
            'user_id',
            "'$username' AS username", // Langsung string, tidak perlu escape
            "'$outlet_name' AS outlet_name",
            "'$digipos_id' AS digipos_id",
            "COUNT(CASE WHEN LOWER(card_type) = 'byu' THEN B.msisdn END) AS so_byu_valid",
            "COUNT(CASE WHEN LOWER(card_type) = 'perdana' THEN B.msisdn END) AS so_perdana_valid",
            "COUNT(B.msisdn) AS so_total_valid"
        ]);
        $builder->join("$tableName C", "B.msisdn = C.msisdn");
        $builder->where('user_id', $user_id);
        $builder->groupBy('user_id');

        $query = $builder->get();
        return $query->getResultArray();
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

    function getScanSummaryDataAdmin($periodeDb){
        $db = \Config\Database::connect();
        $periodeDb = '202502'; // Example value, replace it dynamically
        $tableName = "sellout_barcode_{$periodeDb}_v2"; // Dynamically generate table name

        $builder = $db->table('users u');

        // Subquery for sellout and scan history
        $subquery = $db->table($tableName.' sb')
            ->join('scan_histories t', 'sb.msisdn = t.msisdn')
            ->select('t.user_id')
            ->selectCount("CASE WHEN t.card_type = 'byu' THEN t.msisdn END", 'so_byu_valid')
            ->selectCount("CASE WHEN t.card_type = 'perdana' THEN t.msisdn END", 'so_perdana_valid')
            ->selectCount("t.msisdn", 'so_total_valid')
            ->groupBy('t.user_id')
            ->getCompiledSelect(); // Compile the subquery

        $builder->select('u.fl_name, u.outlet_name, u.digipos_id')
            ->select('COALESCE(s.so_byu_valid, 0) AS so_byu_valid', false)
            ->select('COALESCE(s.so_perdana_valid, 0) AS so_perdana_valid', false)
            ->select('COALESCE(s.so_total_valid, 0) AS so_total_valid', false)
            ->join("($subquery) s", 'u.id = s.user_id', 'left', false)
            ->where('u.status', '1')
            ->orderBy('so_total_valid','DESC')
            ->limit(50);

        $query = $builder->get();
        $result = $query->getResultArray(); // Fetch as an array

        return $result;
    }

    function getScanSummaryCompare($periode){
        $db = \Config\Database::connect();

        $builder = $db->table('scan_compare')
        ->where('update_date LIKE', '2025-02-%') // Filter update_date by February 2025
        ->orderBy('so_total_valid', 'DESC'); // Order by so_total_valid descending
    
        $query = $builder->get();
        $result = $query->getResultArray(); // Fetch as an array

        return $result;
    }
}
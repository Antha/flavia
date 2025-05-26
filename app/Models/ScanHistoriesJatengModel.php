<?php

namespace App\Models;

use CodeIgniter\Model;

class ScanHistoriesJatengModel extends Model
{
    protected $table = 'scan_histories_jateng_jateng';
    protected $primaryKey = 'id';
    protected $allowedFields = ['fl_name','digipos_id','outlet_name','datetime', 'msisdn', 'status','user_id','card_type'];
    
    public function getOneByMsisdn($msisdn)
    {
        return $this->where('msisdn', $msisdn)->first();
    }

    function isTableExists($periode){
        $db = \Config\Database::connect();

        if ($db->tableExists('sellout_barcode_jateng_'.$periode)) {
            $result = "1";
        } else {
            $result = "0";
        }

        return $result;
    }

    function getMaxUpdateDateFull(){
        $db = \Config\Database::connect();
        $builder = $db->table('scan_histories_jateng');
        return $builder->selectMax('datetime','update_date')->get()->getResultArray();
    }

    function getMaxUpdateDateFullUser($user_id){
        $db = \Config\Database::connect();
        $builder = $db->table('scan_histories_jateng');

       $builder->select("COALESCE(MAX(datetime), '2025-01-01') AS update_date", false)
        ->where('user_id', $user_id);

        $result = $builder->get()->getResultArray();

        return $result ?? ['update_date' => '202501'];
    }

    function getMaxUpdateDatescanCompare(){
        $db = \Config\Database::connect();
        $builder = $db->table('scan_compare');
        return $builder->selectMax('update_date')->get()->getResultArray();
    }

    function getLattestSoBarcodeTable(){
        $db = \Config\Database::connect();
        $targetDatabase = 'flavia'; // Ganti dengan nama database yang sesuai

        $builder = $db->table('INFORMATION_SCHEMA.TABLES')
            ->selectMax('TABLE_NAME')
            ->where('TABLE_SCHEMA', $targetDatabase) // Filter berdasarkan database
            ->like('TABLE_NAME', 'sellout_barcode_jateng_%', 'after'); // Filter tabel yang diawali 'sellout_barcode_jateng_'

        $query = $builder->get();
        $results = $query->getRowArray(); // Mengambil hasil sebagai array

        return $results;
    }

    function getScanTotalOnly($user_id,$startDate,$endDate){
        $db = \Config\Database::connect();

        $builder = $db->table('scan_histories_jateng')
            ->select('user_id')
            ->select("COALESCE(COUNT(DISTINCT IF(card_type = 'byu', msisdn, NULL)), 0) AS total_byu", false)
            ->select("COALESCE(COUNT(DISTINCT IF(card_type = 'perdana', msisdn, NULL)), 0) AS total_perdana", false)
            ->select("COALESCE(COUNT(DISTINCT msisdn), 0) AS total_scan", false)
            ->where('user_id', $user_id)
            ->where("datetime >=", $startDate)
            ->where("datetime <=", $endDate)
            ->groupBy('user_id');

        $result = $builder->get()->getRowArray();
        return $result ?? ['user_id' => $user_id, 'total_byu' => 0, 'total_perdana' => 0, 'total_scan' => 0];

    }

    function getScanSummaryCompareAdmin($periode){
        $db = \Config\Database::connect();
        $periodeDb = $periode."%";

        /* New source code compare 
        SELECT user_id,fl_name,outlet_name,digipos_id,id_outlet,
        COUNT(CASE WHEN LOWER(card_type) = 'perdana' THEN AB.msisdn END) so_perdana_valid,
        COUNT(CASE WHEN LOWER(card_type) = 'byu' THEN AB.msisdn END) so_byu_valid,
        COUNT(AB.msisdn) so_total
        FROM
        (SELECT A.id user_id,fl_name,outlet_name,digipos_id,msisdn,card_type
        FROM
        (SELECT id,fl_name,outlet_name,digipos_id
        FROM users)A
        JOIN
        (SELECT user_id,msisdn,card_type 
        FROM `scan_histories_jateng`
        WHERE (`datetime` >= '2025-02-01 00:00:00' AND `datetime` <= '2025-02-28 23:59:59'))B
        ON A.id = B.user_id)AB
        JOIN
        (SELECT CONCAT('62', SUBSTRING(msisdn, 2))msisdn,id_outlet FROM `sellout_barcode_jateng_202502`)C
        ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet
        GROUP BY user_id,fl_name,outlet_name,digipos_id
        */

        $builder = $db->table('scan_compare')
        ->where('update_date LIKE', $periodeDb) // Filter update_date by February 2025
        ->orderBy('so_total_valid', 'DESC'); // Order by so_total_valid descending
    
        $query = $builder->get();
        $result = $query->getResultArray(); // Fetch as an array
       
        return $result;
    }

    //query source sellour barcode new
    //SO BARCODE
    //SELECT * FROM sellout_barcode_jateng_202504 WHERE star_status = 'PAYLOAD' AND regional LIKE 'BALI%'

    //RENEWAL SO
    //SELECT * FROM `RENEWAL_SO_202504` WHERE regional = 'BALINUSRA'

    /*SELECT `area`,regional,cluster,kabupaten,kecamatan,id_outlet,A.msisdn msisdn, B.msisdn renewal_msisdn, package_type
    FROM
    (SELECT CONCAT('62', SUBSTRING(msisdn, 2)) AS msisdn, id_outlet,`area`,regional,cluster,kabupaten,kecamatan
    FROM `sellout_barcode_jateng_raw`)A
    LEFT JOIN
    (SELECT msisdn,package_type FROM `renewal_so_202504`)B
    ON A.msisdn = B.msisdn*/

    function getScanSummaryCompareRealTimeAdmin($periode,$startDate,$endDate){
        $db = \Config\Database::connect();

        $tableName = "sellout_barcode_jateng_".$periode; // Adjust dynamically if needed
        //$tableName_rn = "renewal_so_".$periode;

        // Subquery A: Users
        $subqueryA = $db->table('users')
                        ->select('id AS user_id, fl_name, outlet_name, digipos_id');

        // Subquery B: Scan Histories (filtered by date)
        $subqueryB = $db->table('scan_histories_jateng')
            ->select('user_id, msisdn, card_type')
            ->where("datetime >=", $startDate)
            ->where("datetime <=", $endDate);

        // Combining Subquery A and B (AB)
        $subqueryAB = $db->table("({$subqueryA->getCompiledSelect()}) A")
            ->join("({$subqueryB->getCompiledSelect()}) B", "A.user_id = B.user_id", "inner")
            ->select('A.user_id, A.fl_name, A.outlet_name, A.digipos_id, B.msisdn, B.card_type');

        // Subquery C: Sellout Barcode Table
        $subqueryC = $db->table($tableName)
            ->select("msisdn, id_outlet, package_type");

        // Final Query with COUNT DISTINCT
        $query = $db->table("({$subqueryAB->getCompiledSelect()}) AB")
                    ->join("({$subqueryC->getCompiledSelect()}) C", "AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet", "inner")
                    //->join("({$subqueryD->getCompiledSelect(false)}) D", "AB.msisdn = D.msisdn", "left")                    
                    ->select('AB.user_id, AB.fl_name, AB.outlet_name, AB.digipos_id')
                    ->select("COUNT(DISTINCT CASE WHEN LOWER(AB.card_type) = 'perdana' THEN AB.msisdn END) AS so_perdana_valid", false)
                    ->select("COUNT(DISTINCT CASE WHEN LOWER(AB.card_type) = 'byu' THEN AB.msisdn END) AS so_byu_valid", false)
                    ->select("COUNT(DISTINCT AB.msisdn) AS so_total_valid", false)

                    // count breakdown by package_type
                    ->select("COUNT(CASE WHEN package_type = 'akuisisi' THEN AB.msisdn END) AS so_akuisisi", false)
                    ->select("COUNT(CASE WHEN package_type = 'bonus' THEN AB.msisdn END) AS so_bonus", false)
                    ->select("COUNT(CASE WHEN package_type = 'btl' THEN AB.msisdn END) AS so_btl", false)
                    ->select("COUNT(CASE WHEN package_type = 'core' THEN AB.msisdn END) AS so_core", false)
                    ->select("COUNT(CASE WHEN package_type = 'orbit' THEN AB.msisdn END) AS so_orbit", false)
                    ->select("COUNT(CASE WHEN package_type = 'others' THEN AB.msisdn END) AS so_others", false)
                    ->select("COUNT(CASE WHEN package_type = 'voucher physical' THEN AB.msisdn END) AS so_vf", false)
                    ->select("COUNT(CASE WHEN package_type IS NOT NULL THEN AB.msisdn END) AS so_pt_total", false)
                    ->groupBy('AB.user_id, AB.fl_name, AB.outlet_name, AB.digipos_id')
                    ->orderBy('so_total_valid', 'DESC'); // ORDER BY so_total DESC

        $result = $query->get()->getResultArray();
        $this->write_custom_log($db->getLastQuery());
        return $result;
    }

    function getScanSummaryCompareRealTimeAdminNp($periode,$startDate,$endDate){
        $db = \Config\Database::connect();
        $tableName = "sellout_barcode_jateng_".$periode; // Adjust dynamically if needed
        $tableName_rn = "renewal_so_".$periode;
        // Subquery A: Users
        $subqueryA = $db->table('users')
                        ->select('id AS user_id, fl_name, outlet_name, digipos_id');

        // Subquery B: Scan Histories (filtered by date)
        $subqueryB = $db->table('scan_histories_jateng')
            ->select('user_id, msisdn, card_type')
            ->where("datetime >=", $startDate)
            ->where("datetime <=", $endDate);

        // Combining Subquery A and B (AB)
        $subqueryAB = $db->table("({$subqueryA->getCompiledSelect()}) A")
            ->join("({$subqueryB->getCompiledSelect()}) B", "A.user_id = B.user_id", "inner")
            ->select('A.user_id, A.fl_name, A.outlet_name, A.digipos_id, B.msisdn, B.card_type');

        // Subquery C: Sellout Barcode Table
        $subqueryC = $db->table($tableName)
            ->select("CONCAT('62', SUBSTRING(msisdn, 2)) AS msisdn, id_outlet");

        // Subquery D: Renewal SO Table
        $subqueryD = $db->table($tableName_rn)
        ->select('msisdn, package_type, revenue');

        // Final Query with COUNT DISTINCT
        $query = $db->table("({$subqueryAB->getCompiledSelect()}) AB")
                    ->join("({$subqueryC->getCompiledSelect()}) C", "AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet", "inner")
                    ->join("({$subqueryD->getCompiledSelect(false)}) D", "AB.msisdn = D.msisdn", "left")
                    ->select('AB.user_id, AB.fl_name, AB.outlet_name, AB.digipos_id')
                    ->select("COUNT(DISTINCT CASE WHEN LOWER(AB.card_type) = 'perdana' THEN AB.msisdn END) AS so_perdana_valid", false)
                    ->select("COUNT(DISTINCT CASE WHEN LOWER(AB.card_type) = 'byu' THEN AB.msisdn END) AS so_byu_valid", false)
                    ->select("COUNT(DISTINCT AB.msisdn) AS so_total_valid", false)

                    // Revenue breakdown by package_type
                    ->select("SUM(CASE WHEN D.package_type = 'akuisisi' THEN D.revenue ELSE 0 END) AS rev_akuisisi", false)
                    ->select("SUM(CASE WHEN D.package_type = 'bonus' THEN D.revenue ELSE 0 END) AS rev_bonus", false)
                    ->select("SUM(CASE WHEN D.package_type = 'btl' THEN D.revenue ELSE 0 END) AS rev_btl", false)
                    ->select("SUM(CASE WHEN D.package_type = 'core' THEN D.revenue ELSE 0 END) AS rev_core", false)
                    ->select("SUM(CASE WHEN D.package_type = 'orbit' THEN D.revenue ELSE 0 END) AS rev_orbit", false)
                    ->select("SUM(CASE WHEN D.package_type = 'others' THEN D.revenue ELSE 0 END) AS rev_others", false)
                    ->select("SUM(CASE WHEN D.package_type = 'voucher physical' THEN D.revenue ELSE 0 END) AS rev_voucher_physical", false)
                    ->select("SUM(D.revenue) AS rev_total", false)

                    ->groupBy('AB.user_id, AB.fl_name, AB.outlet_name, AB.digipos_id')
                    ->orderBy('so_total_valid', 'DESC'); // ORDER BY so_total DESC

        $result = $query->get()->getResultArray();

        return $result;
    }

    function getScanSummaryCompareUser($periode,$user_id){
        $db = \Config\Database::connect();
        $periodeDb = $periode."%";

        $builder = $db->table('scan_compare')
        ->where('update_date LIKE', $periodeDb) // Filter update_date by February 2025
        ->where('user_id',$user_id);
    
        $query = $builder->get();
        $result = $query->getResultArray(); // Fetch as an array

        return $result;
    }

    function getScanSummaryCompareRealTimeUser($periode,$user_id,$startDate,$endDate){
        $db = \Config\Database::connect();
        $tableName = 'sellout_barcode_jateng_'.$periode;

        // Subquery A: Users
        $subqueryA = "(SELECT id AS user_id, fl_name, outlet_name, digipos_id FROM users WHERE id = '{$user_id}')";

        // Subquery B: Scan Histories
        $subqueryB = "(SELECT user_id, msisdn, card_type 
                    FROM scan_histories_jateng 
                    WHERE datetime >= '{$startDate}' 
                    AND datetime <= '{$endDate}' 
                    AND user_id = '{$user_id}')";

        // Gabungan Subquery A dan B (AB)
        $subqueryAB = "(SELECT A.user_id, A.fl_name, A.outlet_name, A.digipos_id, B.msisdn, B.card_type 
                        FROM {$subqueryA} A 
                        JOIN {$subqueryB} B ON A.user_id = B.user_id)";

        // Subquery C: Sellout Barcode
        $subqueryC = "(SELECT CONCAT('62', SUBSTRING(msisdn, 2)) AS msisdn, id_outlet 
                    FROM {$tableName})";

        // Query Final
        $queryStr = "SELECT AB.user_id, AB.fl_name, AB.outlet_name, AB.digipos_id, 
                        COUNT(DISTINCT CASE WHEN LOWER(AB.card_type) = 'perdana' THEN AB.msisdn END) AS so_perdana_valid,
                        COUNT(DISTINCT CASE WHEN LOWER(AB.card_type) = 'byu' THEN AB.msisdn END) AS so_byu_valid,
                        COUNT(DISTINCT AB.msisdn) AS so_total_valid
                    FROM {$subqueryAB} AB
                    JOIN {$subqueryC} C ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet
                    GROUP BY AB.user_id, AB.fl_name, AB.outlet_name, AB.digipos_id";

        // Eksekusi Query
        $query = $db->query($queryStr);
        return $query->getResultArray();
    }

    function write_custom_log($message, $filename = 'custom-log.txt')
    {
        $filePath = WRITEPATH . 'logs/' . $filename;
        $time = date('Y-m-d H:i:s');
        $log = "[{$time}] {$message}" . PHP_EOL;

        file_put_contents($filePath, $log, FILE_APPEND);
    }
}
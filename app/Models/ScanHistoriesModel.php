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
        return $builder->selectMax('datetime','update_date')->get()->getResultArray();
    }

    function getMaxUpdateDateFullUser($user_id){
        $db = \Config\Database::connect();
        $builder = $db->table('scan_histories');

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
            ->like('TABLE_NAME', 'sellout_barcode_%', 'after'); // Filter tabel yang diawali 'sellout_barcode_'

        $query = $builder->get();
        $results = $query->getRowArray(); // Mengambil hasil sebagai array

        return $results;
    }

    function getScanTotalOnly($user_id,$startDate,$endDate){
        $db = \Config\Database::connect();

        $builder = $db->table('scan_histories')
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
        FROM `scan_histories`
        WHERE (`datetime` >= '2025-02-01 00:00:00' AND `datetime` <= '2025-02-28 23:59:59'))B
        ON A.id = B.user_id)AB
        JOIN
        (SELECT CONCAT('62', SUBSTRING(msisdn, 2))msisdn,id_outlet FROM `sellout_barcode_202502`)C
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
    //SELECT * FROM SELLOUT_BARCODE_202505 WHERE star_status = 'PAYLOAD' AND regional LIKE 'BALI%'

    //RENEWAL SO
    //SELECT * FROM `RENEWAL_SO_202504` WHERE regional = 'BALINUSRA'

    /*SELECT `area`,regional,cluster,kabupaten,kecamatan,id_outlet,A.msisdn msisdn, B.msisdn renewal_msisdn, package_type,revenue
    FROM
    (SELECT CONCAT('62', SUBSTRING(msisdn, 2)) AS msisdn, id_outlet,`area`,regional,cluster,kabupaten,kecamatan
    FROM `sellout_barcode_raw`)A
    LEFT JOIN
    (SELECT msisdn,package_type,revenue FROM `renewal_so_202504`)B
    ON A.msisdn = B.msisdn*/

    //get data all flavia
    /*SELECT DISTINCT user_id,fl_name,outlet_name,digipos_id,AB.msisdn,scan_time,card_type,package_type
        FROM
        (SELECT A.id user_id,fl_name,outlet_name,digipos_id,msisdn,scan_time,card_type
        FROM
        (SELECT id,fl_name,outlet_name,digipos_id
        FROM users)A
        RIGHT JOIN
        (SELECT user_id,msisdn,card_type,`datetime` scan_time 
        FROM `scan_histories`
        WHERE (`datetime` >= '2025-05-01 00:00:00' AND `datetime` <= '2025-05-31 23:59:59'))B
        ON A.id = B.user_id)AB
        LEFT JOIN
        (SELECT CONCAT('62', SUBSTRING(msisdn, 2))msisdn,id_outlet,package_type FROM `sellout_barcode_202505`)C
        ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet*/

    //new get all data
    /*
        SELECT 
            AB.user_id, 
            AB.fl_name, 
            AB.outlet_name, 
            AB.digipos_id,
            AB.msisdn,
            AB.card_type,
            C.package_type,
            C.revenue
        FROM (
            SELECT 
                U.id AS user_id, 
                U.fl_name, 
                U.outlet_name, 
                U.digipos_id, 
                SH.msisdn, 
                SH.card_type
            FROM users U
            INNER JOIN scan_histories SH 
                ON U.id = SH.user_id
            WHERE SH.datetime >= '2025-05-01 00:00:00' AND SH.datetime <= '2025-05-31 23:59:59'
        ) AS AB

        INNER JOIN sellout_barcode_202505 C 
            ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet
    */

    /*SELECT user_id, 
    fl_name, 
    outlet_name, 
    digipos_id,
    AX.msisdn,
    card_type,
    package_type,
    `revenue`,
    flag_nsr_mtd,
    rev_nsr_mtd
FROM 
(SELECT 
    AB.user_id, 
    AB.fl_name, 
    AB.outlet_name, 
    AB.digipos_id,
    AB.msisdn,
    AB.card_type,
    C.package_type,
    C.`revenue`
FROM (
    SELECT 
	U.id AS user_id, 
	U.fl_name, 
	U.outlet_name, 
	U.digipos_id, 
	SH.msisdn, 
	SH.card_type
    FROM users U
    INNER JOIN scan_histories SH 
	ON U.id = SH.user_id
    WHERE SH.datetime >= '2025-05-01 00:00:00' AND SH.datetime <= '2025-05-31 23:59:59'
) AS AB

INNER JOIN sellout_barcode_202505 C 
    ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet)AX
    LEFT JOIN
(SELECT msisdn,flag_nsr_mtd,rev_nsr_mtd FROM `nsr_merge_202505`)BX
ON AX.msisdn = BX.msisdn */

//scan history balnus complete
/*SELECT
        AB.user_id, 
        AB.fl_name, 
        AB.outlet_name, 
        AB.digipos_id,
        AB.msisdn,
        AB.card_type,
        AB.scan_date,
        AB.BRANCH branch,
        AB.CLUSTER cluster,
	CASE WHEN C.msisdn IS NULL THEN "Not Valid" ELSE "Valid" END status_valid
    FROM (
        SELECT 
        U.id AS user_id, 
        U.fl_name, 
        U.outlet_name, 
        U.digipos_id, 
        SH.msisdn, 
        SH.card_type,
        SH.datetime scan_date,
        OP.BRANCH,
        OP.CLUSTER
        FROM users U
        INNER JOIN scan_histories SH 
        ON U.id = SH.user_id
        LEFT JOIN `outlet_pjp_area_2025` OP
        ON U.digipos_id = OP.ID_OUTLET
        WHERE SH.datetime >= '2025-07-01 00:00:00' AND SH.datetime <= '2025-07-31 23:59:59'
    ) AS AB

    LEFT JOIN (SELECT DISTINCT msisdn,id_outlet,package_type,revenue FROM sellout_barcode_202507) C 
        ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet
    */

    /*get flavia area
        SELECT
        AB.user_id, 
        AB.fl_name, 
        AB.outlet_name, 
        AB.digipos_id,
        AB.CLUSTER cluster,
        AB.msisdn,
        C.msisdn msisdn_so_barcode_compare,
        AB.card_type,
        C.package_type package_type_renewal,
        C.revenue revenue_renewal,
        CASE WHEN C.msisdn IS NULL THEN "Not Valid" ELSE "Valid" END status_valid,
        AB.scan_date
    FROM (
        SELECT 
        U.id AS user_id, 
        U.fl_name, 
        U.outlet_name, 
        U.digipos_id, 
        SH.msisdn, 
        SH.card_type,
        SH.datetime scan_date,
        OP.CLUSTER
        FROM users U
        INNER JOIN scan_histories SH 
        ON U.id = SH.user_id
        LEFT JOIN `outlet_pjp_area_2025` OP
        ON U.digipos_id = OP.ID_OUTLET
        WHERE SH.datetime >= '2025-06-01 00:00:00' AND SH.datetime <= '2025-06-31 23:59:59'
    ) AS AB

    LEFT JOIN (SELECT DISTINCT msisdn,id_outlet,package_type,revenue FROM sellout_barcode_202506) C 
        ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet
     */

    function getScanSummaryCompareRealTimeAdmin($periode,$startDate,$endDate){
        $db = \Config\Database::connect();

        $tableName = "sellout_barcode_".$periode; // Adjust dynamically if needed
        //$tableName_rn = "renewal_so_".$periode;

        // Subquery A: Users
        $subqueryA = $db->table('users')
                        ->select('id AS user_id, fl_name, outlet_name, digipos_id');

        // Subquery B: Scan Histories (filtered by date)
        $subqueryB = $db->table('scan_histories')
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

        return $result;
    }

    function getScanSummaryCompareRealTimeAdminNp($periode,$startDate,$endDate){
        $db = \Config\Database::connect();
        $tableName = "sellout_barcode_".$periode; // Adjust dynamically if needed
        $tableName_rn = "renewal_so_".$periode;
        // Subquery A: Users
        $subqueryA = $db->table('users')
                        ->select('id AS user_id, fl_name, outlet_name, digipos_id');

        // Subquery B: Scan Histories (filtered by date)
        $subqueryB = $db->table('scan_histories')
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
        $tableName = 'sellout_barcode_'.$periode;

        // Subquery A: Users
        $subqueryA = "(SELECT id AS user_id, fl_name, outlet_name, digipos_id FROM users WHERE id = '{$user_id}')";

        // Subquery B: Scan Histories
        $subqueryB = "(SELECT user_id, msisdn, card_type 
                    FROM scan_histories 
                    WHERE datetime >= '{$startDate}' 
                    AND datetime <= '{$endDate}' 
                    AND user_id = '{$user_id}')";

        // Gabungan Subquery A dan B (AB)
        $subqueryAB = "(SELECT A.user_id, A.fl_name, A.outlet_name, A.digipos_id, B.msisdn, B.card_type 
                        FROM {$subqueryA} A 
                        JOIN {$subqueryB} B ON A.user_id = B.user_id)";

        // Subquery C: Sellout Barcode
        $subqueryC = "(SELECT msisdn, id_outlet,package_type 
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

    function getScanSummaryAdminFlaviaAreaCluster($periode,$startDate,$endDate){
        $db = \Config\Database::connect();
        // Final Query with COUNT DISTINCT
        $sql = "SELECT regional,AA.CLUSTER cluster,fl_register,
                    COUNT(fl_scan) fl_scan,
                    ROUND((COUNT(fl_scan)/fl_register)*100,1) fl_scan_to_register,
                    COUNT(CASE WHEN fl_scan > 1 THEN fl_scan END) fl_aktif,
                    IFNULL(ROUND((COUNT(CASE WHEN fl_scan > 1 THEN fl_scan END)/COUNT(fl_scan))*100,1),0) fl_aktif_to_register,
                    SUM(msisdn_scan) msisdn_scan,
                    SUM(fl_scan) msisdn_aktif_so,
                    SUM(renewal) renewal,
                    IFNULL(ROUND((SUM(renewal)/SUM(fl_scan))*100,1),0) renewal_to_so,
                    SUM(rev_renewal) rev_renewal
                    FROM
                    (SELECT REGIONAL regional,CLUSTER, COUNT(DISTINCT id) fl_register 
                    FROM `users` A
                    LEFT JOIN outlet_pjp_area_2025 B
                    ON A.digipos_id = B.`ID_OUTLET`
                    WHERE username NOT IN('admin','dewa','bagus01') AND `status` = '1' AND CLUSTER IS NOT NULL
                    GROUP BY CLUSTER)AA
                    LEFT JOIN
                    (SELECT cluster,fl_name,
                    COUNT(CASE WHEN status_valid = 'valid' THEN msisdn END) fl_scan,
                    COUNT(msisdn) msisdn_scan,
                    COUNT(package_type_renewal) renewal,
                    SUM(revenue_renewal) rev_renewal
                    FROM
                    (SELECT
                    AB.user_id, 
                    AB.fl_name, 
                    AB.outlet_name, 
                    AB.digipos_id,
                    AB.CLUSTER cluster,
                    AB.msisdn,
                    C.msisdn msisdn_so_barcode_compare,
                    AB.card_type,
                    C.package_type package_type_renewal,
                    C.revenue revenue_renewal,
                    CASE WHEN C.msisdn IS NULL THEN 'Not Valid' ELSE 'Valid' END status_valid,
                    AB.scan_date
                    FROM (
                    SELECT DISTINCT
                    U.id AS user_id, 
                    U.fl_name, 
                    U.outlet_name, 
                    U.digipos_id, 
                    SH.msisdn, 
                    SH.card_type,
                    SH.datetime scan_date,
                    OP.CLUSTER
                    FROM users U
                    INNER JOIN scan_histories SH 
                    ON U.id = SH.user_id
                    LEFT JOIN `outlet_pjp_area_2025` OP
                    ON U.digipos_id = OP.ID_OUTLET
                    WHERE SH.datetime >= '{$startDate}' AND SH.datetime <= '{$endDate}' 
                    ) AS AB
                    LEFT JOIN (SELECT DISTINCT msisdn,id_outlet,package_type,revenue FROM sellout_barcode_{$periode}) C 
                    ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet)BBSC
                    WHERE cluster IS NOT NULL 
                    GROUP BY cluster,fl_name)BB
                    ON AA.cluster = BB.cluster
                    GROUP BY AA.cluster
                    ORDER BY regional, AA.cluster";

        $query = $db->query($sql);
        if($query)return $query->getResultArray();
    }

    function getScanSummaryAdminFlaviaAreaRegional($periode,$startDate,$endDate){
        $db = \Config\Database::connect();
        // Final Query with COUNT DISTINCT
        $sql = "SELECT AA.regional regional,fl_register,
                    COUNT(fl_scan) fl_scan,
                    ROUND((COUNT(fl_scan)/fl_register)*100,1) fl_scan_to_register,
                    COUNT(CASE WHEN fl_scan > 1 THEN fl_scan END) fl_aktif,
                    IFNULL(ROUND((COUNT(CASE WHEN fl_scan > 1 THEN fl_scan END)/COUNT(fl_scan))*100,1),0) fl_aktif_to_register,
                    SUM(msisdn_scan) msisdn_scan,
                    SUM(fl_scan) msisdn_aktif_so,
                    SUM(renewal) renewal,
                    IFNULL(ROUND((SUM(renewal)/SUM(fl_scan))*100,1),0) renewal_to_so,
                    SUM(rev_renewal) rev_renewal
                    FROM
                    (SELECT REGIONAL regional,CLUSTER, COUNT(DISTINCT CASE WHEN CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM') THEN id END) fl_register
                    FROM `users` A
                    LEFT JOIN outlet_pjp_area_2025 B
                    ON A.digipos_id = B.`ID_OUTLET`
                    WHERE username NOT IN('admin','dewa','bagus01') AND `status` = '1' AND CLUSTER IS NOT NULL
                    GROUP BY REGIONAL)AA
                    LEFT JOIN
                    (SELECT regional,fl_name,
                    COUNT(CASE WHEN status_valid = 'valid' AND (CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM'))THEN msisdn END) fl_scan,
                    COUNT(CASE WHEN CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM') THEN msisdn END) msisdn_scan,
                    COUNT(CASE WHEN CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM') THEN package_type_renewal END) renewal,
                    SUM(CASE WHEN CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM') THEN revenue_renewal END) rev_renewal
                    FROM
                    (SELECT
                    AB.user_id, 
                    AB.fl_name, 
                    AB.outlet_name, 
                    AB.digipos_id,
                    AB.REGIONAL regional,
                    AB.CLUSTER cluster,
                    AB.msisdn,
                    C.msisdn msisdn_so_barcode_compare,
                    AB.card_type,
                    C.package_type package_type_renewal,
                    C.revenue revenue_renewal,
                    CASE WHEN C.msisdn IS NULL THEN 'Not Valid' ELSE 'Valid' END status_valid,
                    AB.scan_date
                    FROM (
                    SELECT DISTINCT
                    U.id AS user_id, 
                    U.fl_name, 
                    U.outlet_name, 
                    U.digipos_id, 
                    SH.msisdn, 
                    SH.card_type,
                    SH.datetime scan_date,
                    OP.REGIONAL,
                    OP.CLUSTER
                    FROM users U
                    INNER JOIN scan_histories SH 
                    ON U.id = SH.user_id
                    LEFT JOIN `outlet_pjp_area_2025` OP
                    ON U.digipos_id = OP.ID_OUTLET
                    WHERE SH.datetime >= '{$startDate}' AND SH.datetime <= '{$endDate}' 
                    ) AS AB
                    LEFT JOIN (SELECT DISTINCT msisdn,id_outlet,package_type,revenue FROM sellout_barcode_{$periode}) C 
                    ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet)BBSC
                    WHERE cluster IS NOT NULL 
                    GROUP BY regional,fl_name)BB
                    ON AA.regional = BB.regional
                    GROUP BY regional
                    ORDER BY regional";

        $query = $db->query($sql);
        if($query)return $query->getResultArray();
    }

    function getScanSummaryAdminFlaviaAreaAll($periode,$startDate,$endDate){
        $db = \Config\Database::connect();
        // Final Query with COUNT DISTINCT
        $sql = "SELECT AA.areas areas,fl_register,
                COUNT(fl_scan) fl_scan,
                ROUND((COUNT(fl_scan)/fl_register)*100,1) fl_scan_to_register,
                COUNT(CASE WHEN fl_scan > 1 THEN fl_scan END) fl_aktif,
                IFNULL(ROUND((COUNT(CASE WHEN fl_scan > 1 THEN fl_scan END)/COUNT(fl_scan))*100,1),0) fl_aktif_to_register,
                SUM(msisdn_scan) msisdn_scan,
                SUM(fl_scan) msisdn_aktif_so,
                SUM(renewal) renewal,
                IFNULL(ROUND((SUM(renewal)/SUM(fl_scan))*100,1),0) renewal_to_so,
                SUM(rev_renewal) rev_renewal
                FROM
                (SELECT 'AREA 3' areas, COUNT(DISTINCT CASE WHEN CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM') THEN id END) fl_register
                FROM `users` A
                LEFT JOIN outlet_pjp_area_2025 B
                ON A.digipos_id = B.`ID_OUTLET`
                WHERE username NOT IN('admin','dewa','bagus01') AND `status` = '1' AND CLUSTER IS NOT NULL
                GROUP BY areas)AA
                LEFT JOIN
                (SELECT 'AREA 3' areas,fl_name,
                COUNT(CASE WHEN status_valid = 'valid' AND (CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM'))THEN msisdn END) fl_scan,
                COUNT(CASE WHEN CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM') THEN msisdn END) msisdn_scan,
                COUNT(CASE WHEN CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM') THEN package_type_renewal END) renewal,
                SUM(CASE WHEN CLUSTER IN('BALI BARAT','BALI TIMUR','LOMBOK') OR REGIONAL IN('JATENG-DIY','JATIM') THEN revenue_renewal END) rev_renewal
                FROM
                (SELECT
                AB.user_id, 
                AB.fl_name, 
                AB.outlet_name, 
                AB.digipos_id,
                AB.REGIONAL regional,
                AB.CLUSTER cluster,
                AB.msisdn,
                C.msisdn msisdn_so_barcode_compare,
                AB.card_type,
                C.package_type package_type_renewal,
                C.revenue revenue_renewal,
                CASE WHEN C.msisdn IS NULL THEN 'Not Valid' ELSE 'Valid' END status_valid,
                AB.scan_date
                FROM (
                SELECT 
                U.id AS user_id, 
                U.fl_name, 
                U.outlet_name, 
                U.digipos_id, 
                SH.msisdn, 
                SH.card_type,
                SH.datetime scan_date,
                OP.REGIONAL,
                OP.CLUSTER
                FROM users U
                INNER JOIN scan_histories SH 
                ON U.id = SH.user_id
                LEFT JOIN `outlet_pjp_area_2025` OP
                ON U.digipos_id = OP.ID_OUTLET
                WHERE SH.datetime >= '{$startDate}' AND SH.datetime <= '{$endDate}'  
                ) AS AB
                LEFT JOIN (SELECT DISTINCT msisdn,id_outlet,package_type,revenue FROM sellout_barcode_202506) C 
                ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet)BBSC
                WHERE cluster IS NOT NULL 
                GROUP BY areas,fl_name)BB
                ON AA.areas = BB.areas
                GROUP BY AA.areas";

        $query = $db->query($sql);
        if($query)return $query->getResultArray();
    }

    function getScanSummaryAdminFlaviaAreaFl($periode,$startDate,$endDate){
        $db = \Config\Database::connect();
        // Final Query with COUNT DISTINCT
        $sql = "SELECT
                    AB.user_id, 
                    AB.fl_name, 
                    AB.outlet_name, 
                    AB.CLUSTER cluster,
                    COUNT(AB.msisdn) msisdn_scan,
                    COUNT(CASE WHEN C.msisdn IS NOT NULL THEN AB.msisdn END) msisdn_aktif_so,
                    COUNT(CASE WHEN C.msisdn IS NOT NULL THEN C.package_type END) renewal,
                    IFNULL(SUM(CASE WHEN C.msisdn IS NOT NULL THEN C.revenue END),0) revenue_renewal
                FROM (
                    SELECT DISTINCT
                    U.id AS user_id, 
                    U.fl_name, 
                    U.outlet_name, 
                    U.digipos_id, 
                    SH.msisdn, 
                    SH.card_type,
                    SH.datetime scan_date,
                    OP.CLUSTER
                    FROM users U
                    INNER JOIN scan_histories SH 
                    ON U.id = SH.user_id
                    LEFT JOIN `outlet_pjp_area_2025` OP
                    ON U.digipos_id = OP.ID_OUTLET
                    WHERE SH.datetime >= '{$startDate}' AND SH.datetime <= '{$endDate}'
                ) AS AB

                LEFT JOIN (SELECT DISTINCT msisdn,id_outlet,package_type,revenue 
                FROM sellout_barcode_{$periode}) C 
                    ON AB.msisdn = C.msisdn AND AB.digipos_id = C.id_outlet
                GROUP BY 1,2,3
                ORDER BY COUNT(AB.msisdn) DESC";

        $query = $db->query($sql);
        if($query)return $query->getResultArray();
    }
}
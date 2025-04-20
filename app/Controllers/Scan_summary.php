<?php

namespace App\Controllers;
use App\Models\ScanHistoriesModel;
use DateTime;

use function PHPUnit\Framework\isNull;

class Scan_summary extends BaseController
{
    protected $session_user;

    public function __construct()
    {
        $this->session_user = session();
    }

    public function index()
    {
        if(session()->get('user_level') == 'admin')
        {
            return redirect()->to('/report/admin_report');
        }else{
            return redirect()->to('/report/user_report');
        }
    }

    public function admin_report_real_time()
    {
        // Cek jika user bukan admin, redirect ke halaman lain
        if (session()->get('user_level') !== 'admin') {
            return redirect()->to('/report/user_report');
        }

        $scan_model = new ScanHistoriesModel();
        // Jika request berasal dari AJAX
        if ($this->request->isAJAX()) {
            // Jika request bukan AJAX, tampilkan halaman normal
            $maxUpdateDateFull = $scan_model->getMaxUpdateDateFull();
            
            //$maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['update_date']));
            //$varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['update_date']));
            $displayInputDate = date('Ym', strtotime($maxUpdateDateFull[0]['update_date']));

            $periode = $this->request->getPost('periode_data') ?? $displayInputDate; // Gunakan default jika kosong

            // Validasi periode
            // Validasi input periode
            $validation = \Config\Services::validation();
            $validation->setRules([
                'periode_data' => 'required|min_length[6]|max_length[6]|numeric'
            ]);

            if (!$validation->run(['periode_data' => $periode])) {
                return $this->response->setJSON(['error' => 'Periode tidak valid!']);
            }

            // Cek apakah tabel dengan nama periode tersedia
            if ($scan_model->isTableExists($periode) == "0") {
                return $this->response->setJSON(['error' => 'No Data Available In ' . $periode]);
            }

            $maxUpdateDate = DateTime::createFromFormat('Ym', $periode)->format('F Y');
            $varMaxDate = DateTime::createFromFormat('Ym', $periode)->format('Y-m-');
            
            // Ensure $periode is in 'YYYYMM' format and convert it to 'YYYY-MM-01'
            $periodeFormatted = substr($periode, 0, 4) . '-' . substr($periode, 4, 2) . '-01';

            $startDate = date('Y-m-01 00:00:00', strtotime($periodeFormatted));
            $endDate = date('Y-m-t 23:59:59', strtotime($periodeFormatted));

            $resumeScan = $scan_model->getScanSummaryCompareRealTimeAdmin($periode,$startDate,$endDate);

            if (empty($resumeScan)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'error' => 'No data found',
                    'data'    => []
                ]);
            }

            // Kirim response dalam format JSON
            return $this->response->setJSON([
                'maxUpdateDate' => $maxUpdateDate,
                'resumeScan' => $resumeScan
            ]);
        }

        // Jika request bukan AJAX, tampilkan halaman normal
        $maxUpdateDateFull = $scan_model->getMaxUpdateDateFull();
        
        if (!empty($maxUpdateDateFull[0]['update_date'])) {
            $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['update_date']));
            $varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['update_date']));
            $displayInputDate = date('Ym', strtotime($maxUpdateDateFull[0]['update_date']));
        } else {
            $maxUpdateDate = "No Data Found";
            $varMaxDate = "0000-00-";
            $displayInputDate = "202501";
        }

         // Ensure $periode is in 'YYYYMM' format and convert it to 'YYYY-MM-01'
         $periodeFormatted = substr($displayInputDate, 0, 4) . '-' . substr($displayInputDate, 4, 2) . '-01';

         $startDate = date('Y-m-01 00:00:00', strtotime($periodeFormatted));
         $endDate = date('Y-m-t 23:59:59', strtotime($periodeFormatted));

         $resumeScan = $scan_model->getScanSummaryCompareRealTimeAdmin($displayInputDate,$startDate,$endDate);

        $data = [
            'maxUpdateDate' => $maxUpdateDate,
            'displayInputDate' => $displayInputDate,
            'resumeScan' => $scan_model->getScanSummaryCompareAdmin($varMaxDate)
        ];

        return view('scan_summary_admin_page',$data);
    }

    public function admin_report_real_time_new_program()
    {
        // Cek jika user bukan admin, redirect ke halaman lain
        if (session()->get('user_level') !== 'admin') {
            return redirect()->to('/report/user_report');
        }

        $scan_model = new ScanHistoriesModel();
        // Jika request berasal dari AJAX
        if ($this->request->isAJAX()) {
            // Jika request bukan AJAX, tampilkan halaman normal
            $maxUpdateDateFull = $scan_model->getMaxUpdateDateFull();
            
            //$maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['update_date']));
            //$varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['update_date']));
            $displayInputDate = date('Ym', strtotime($maxUpdateDateFull[0]['update_date']));

            $periode = $this->request->getPost('periode_data') ?? $displayInputDate; // Gunakan default jika kosong

            // Validasi periode
            // Validasi input periode
            $validation = \Config\Services::validation();
            $validation->setRules([
                'periode_data' => 'required|min_length[6]|max_length[6]|numeric'
            ]);

            if (!$validation->run(['periode_data' => $periode])) {
                return $this->response->setJSON(['error' => 'Periode tidak valid!']);
            }

            // Cek apakah tabel dengan nama periode tersedia
            if ($scan_model->isTableExists($periode) == "0") {
                return $this->response->setJSON(['error' => 'No Data Available In ' . $periode]);
            }

            $maxUpdateDate = DateTime::createFromFormat('Ym', $periode)->format('F Y');
            $varMaxDate = DateTime::createFromFormat('Ym', $periode)->format('Y-m-');
            
            // Ensure $periode is in 'YYYYMM' format and convert it to 'YYYY-MM-01'
            $periodeFormatted = substr($periode, 0, 4) . '-' . substr($periode, 4, 2) . '-01';

            if($periode = '202504'){
                $startDate = date('2025-04-20 00:00:00', strtotime($periodeFormatted));
            }else{
                $startDate = date('Y-m-01 00:00:00', strtotime($periodeFormatted));
            }
            
            $endDate = date('Y-m-t 23:59:59', strtotime($periodeFormatted));

            $resumeScan = $scan_model->getScanSummaryCompareRealTimeAdminNp($periode,$startDate,$endDate);

            if (empty($resumeScan)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'error' => 'No data found',
                    'data'    => []
                ]);
            }

            // Kirim response dalam format JSON
            return $this->response->setJSON([
                'maxUpdateDate' => $maxUpdateDate,
                'resumeScan' => $resumeScan
            ]);
        }

        // Jika request bukan AJAX, tampilkan halaman normal
        $maxUpdateDateFull = $scan_model->getMaxUpdateDateFull();
        
        if (!empty($maxUpdateDateFull[0]['update_date'])) {
            $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['update_date']));
            $varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['update_date']));
            $displayInputDate = date('Ym', strtotime($maxUpdateDateFull[0]['update_date']));
        } else {
            $maxUpdateDate = "No Data Found";
            $varMaxDate = "0000-00-";
            $displayInputDate = "202501";
        }

         // Ensure $periode is in 'YYYYMM' format and convert it to 'YYYY-MM-01'
         $periodeFormatted = substr($displayInputDate, 0, 4) . '-' . substr($displayInputDate, 4, 2) . '-01';

         $startDate = date('Y-m-01 00:00:00', strtotime($periodeFormatted));
         $endDate = date('Y-m-t 23:59:59', strtotime($periodeFormatted));

         $resumeScan = $scan_model->getScanSummaryCompareRealTimeAdmin($displayInputDate,$startDate,$endDate);

        $data = [
            'maxUpdateDate' => $maxUpdateDate,
            'displayInputDate' => $displayInputDate,
            'resumeScan' => $scan_model->getScanSummaryCompareAdmin($varMaxDate)
        ];

        return view('scan_summary_admin_page_np',$data);
    }

    public function admin_report()
    {
        // Cek jika user bukan admin, redirect ke halaman lain
        if (session()->get('user_level') !== 'admin') {
            return redirect()->to('/report/user_report');
        }

        $scan_model = new ScanHistoriesModel();
        // Jika request berasal dari AJAX
        if ($this->request->isAJAX()) {
            // Jika request bukan AJAX, tampilkan halaman normal
            $maxUpdateDateFull = $scan_model->getMaxUpdateDatescanCompare();
            
            $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['update_date']));
            $varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['update_date']));
            $displayInputDate = date('Ym', strtotime($maxUpdateDateFull[0]['update_date']));

            $periode = $this->request->getPost('periode_data') ?? $displayInputDate; // Gunakan default jika kosong

            // Validasi periode
            // Validasi input periode
            $validation = \Config\Services::validation();
            $validation->setRules([
                'periode_data' => 'required|min_length[6]|max_length[6]|numeric'
            ]);

            if (!$validation->run(['periode_data' => $periode])) {
                return $this->response->setJSON(['error' => 'Periode tidak valid!']);
            }

            // Cek apakah tabel dengan nama periode tersedia
            if ($scan_model->isTableExists($periode) == "0") {
                return $this->response->setJSON(['error' => 'No Data Available In ' . $periode]);
            }

            $maxUpdateDate = DateTime::createFromFormat('Ym', $periode)->format('F Y');
            $varMaxDate = DateTime::createFromFormat('Ym', $periode)->format('Y-m-');
            $displayInputDate = $periode;
            $resumeScan = $scan_model->getScanSummaryCompareAdmin($varMaxDate);

            if (empty($resumeScan)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'error' => 'No data found',
                    'data'    => []
                ]);
            }

            // Kirim response dalam format JSON
            return $this->response->setJSON([
                'maxUpdateDate' => $maxUpdateDate,
                'resumeScan' => $resumeScan
            ]);
        }

        // Jika request bukan AJAX, tampilkan halaman normal
        $maxUpdateDateFull = $scan_model->getMaxUpdateDatescanCompare();
        
        if (!empty($maxUpdateDateFull[0]['update_date'])) {
            $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['update_date']));
            $varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['update_date']));
            $displayInputDate = date('Ym', strtotime($maxUpdateDateFull[0]['update_date']));
        } else {
            $maxUpdateDate = "No Data Found";
            $varMaxDate = "0000-00-";
            $displayInputDate = "202501";
        }

        $data = [
            'maxUpdateDate' => $maxUpdateDate,
            'displayInputDate' => $displayInputDate,
            'resumeScan' => $scan_model->getScanSummaryCompareAdmin($varMaxDate)
        ];

        return view('scan_summary_admin_page',$data);
    }

    public function user_report_real_time()
    {
        if (session()->get('user_level') !== 'user') {
            return redirect()->to('/report/admin_report');
        }

        $scan_model = new ScanHistoriesModel();
        $user_id = $this->session_user->get('user_id');

        // Jika request berasal dari AJAX
        if ($this->request->isAJAX()) {
            $maxUpdateDateFull = $scan_model->getMaxUpdateDateFullUser($user_id);
            
            $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['update_date']));
            $varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['update_date']));
            $displayInputDate = date('Ym', strtotime($maxUpdateDateFull[0]['update_date']));

            $periode = $this->request->getPost('periode_data') ?? $displayInputDate; // Gunakan default jika kosong

            // Validasi periode
            // Validasi input periode
            $validation = \Config\Services::validation();
            $validation->setRules([
                'periode_data' => 'required|min_length[6]|max_length[6]|numeric'
            ]);

            if (!$validation->run(['periode_data' => $periode])) {
                return $this->response->setJSON(['error' => 'Periode tidak valid!']);
            }

            // Cek apakah tabel dengan nama periode tersedia
            if ($scan_model->isTableExists($periode) == "0") {
                return $this->response->setJSON(['error' => 'No Data Available In ' . $periode]);
            }

            // Ensure $periode is in 'YYYYMM' format and convert it to 'YYYY-MM-01'
            $periodeFormatted = substr($periode, 0, 4) . '-' . substr($periode, 4, 2) . '-01';

            $startDate = date('Y-m-01 00:00:00', strtotime($periodeFormatted));
            $endDate = date('Y-m-t 23:59:59', strtotime($periodeFormatted));

            $displayInputDate = $periode;

            $resultArrDataTotal = $scan_model->getScanTotalOnly($user_id,$startDate,$endDate);
            if(!$resultArrDataTotal){
                return $this->response->setJSON(['error' => 'No Data Available In ' . $periode]);
            }

            $resumeScan = $scan_model->getScanSummaryCompareRealTimeUser($periode,$user_id,$startDate,$endDate);
            // Kirim response dalam format JSON
            return $this->response->setJSON([
                'maxUpdateDate' => $maxUpdateDate,
                'resultDataByu' => $resultArrDataTotal['total_byu'],
                'resultDataPerdana' => $resultArrDataTotal['total_perdana'],
                'resultDataTotal' => $resultArrDataTotal['total_scan'],
                'resumeScan' => $resumeScan,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        }

        // Jika request bukan AJAX, tampilkan halaman normal
        $maxUpdateDateFullUser = $scan_model->getMaxUpdateDateFullUser($user_id);
       
        $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFullUser[0]['update_date']));
        $displayInputDate = date('Ym', strtotime($maxUpdateDateFullUser[0]['update_date']));

        $startDate = date('Y-m-01', strtotime($maxUpdateDateFullUser[0]['update_date']));
        $endDate = date('Y-m-t', strtotime($maxUpdateDateFullUser[0]['update_date']));
          
        $resultArrDataTotal = $scan_model->getScanTotalOnly($user_id,$startDate,$endDate);
       
        if(!$resultArrDataTotal){
            return $this->response->setJSON(['error' => 'No Data Available In ' . $displayInputDate]);
        }

        $resumeScan = $scan_model->getScanSummaryCompareRealTimeUser($displayInputDate,$user_id,$startDate,$endDate);

        $data = [
            'maxUpdateDate' => $maxUpdateDate,
            'resultDataByu' => $resultArrDataTotal['total_byu'],
            'resultDataPerdana' => $resultArrDataTotal['total_perdana'],
            'resultDataTotal' => $resultArrDataTotal['total_scan'],
            'displayInputDate' => $displayInputDate,
            'resumeScan' => $resumeScan
        ];

        return view('scan_summary_user_page_realtime', $data);
        
    }

    public function user_report()
    {
        if (session()->get('user_level') !== 'user') {
            return redirect()->to('/report/admin_report');
        }

        $scan_model = new ScanHistoriesModel();
        $user_id = session()->get('user_id');

        // Jika request berasal dari AJAX
        if ($this->request->isAJAX()) {
            $maxUpdateDateFull = $scan_model->getMaxUpdateDatescanCompare();
            
            $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['update_date']));
            $varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['update_date']));
            $displayInputDate = date('Ym', strtotime($maxUpdateDateFull[0]['update_date']));

            $periode = $this->request->getPost('periode_data') ?? $displayInputDate; // Gunakan default jika kosong

            // Validasi periode
            // Validasi input periode
            $validation = \Config\Services::validation();
            $validation->setRules([
                'periode_data' => 'required|min_length[6]|max_length[6]|numeric'
            ]);

            if (!$validation->run(['periode_data' => $periode])) {
                return $this->response->setJSON(['error' => 'Periode tidak valid!']);
            }

            // Cek apakah tabel dengan nama periode tersedia
            if ($scan_model->isTableExists($periode) == "0") {
                return $this->response->setJSON(['error' => 'No Data Available In ' . $periode]);
            }

            // Ensure $periode is in 'YYYYMM' format and convert it to 'YYYY-MM-01'
            $periodeFormatted = substr($periode, 0, 4) . '-' . substr($periode, 4, 2) . '-01';

            $startDate = date('Y-m-01 00:00:00', strtotime($periodeFormatted));
            $endDate = date('Y-m-t 23:59:59', strtotime($periodeFormatted));

            $displayInputDate = $periode;

            $resultArrDataTotal = $scan_model->getScanTotalOnly($user_id,$startDate,$endDate);
            if(!$resultArrDataTotal){
                return $this->response->setJSON(['error' => 'No Data Available In ' . $periode]);
            }

            $resumeScan = $scan_model->getScanSummaryCompareUser($varMaxDate, $user_id);
            // Kirim response dalam format JSON
            return $this->response->setJSON([
                'maxUpdateDate' => $maxUpdateDate,
                'resultDataByu' => $resultArrDataTotal['total_byu'],
                'resultDataPerdana' => $resultArrDataTotal['total_perdana'],
                'resultDataTotal' => $resultArrDataTotal['total_scan'],
                'resumeScan' => $resumeScan,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        }

        // Jika request bukan AJAX, tampilkan halaman normal
        $maxUpdateDateFullUser = $scan_model->getMaxUpdateDatescanCompare();

        if (!empty($maxUpdateDateFullUser[0]['update_date'])) {
            $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFullUser[0]['update_date']));
            $varMaxDate = date('Y-m-', strtotime($maxUpdateDateFullUser[0]['update_date']));
            $displayInputDate = date('Ym', strtotime($maxUpdateDateFullUser[0]['update_date']));
        } else {
            $maxUpdateDate = "ND 00 ";
            $varMaxDate = "0000-00-";
            $displayInputDate = "0000";
        }
        
        $startDate = date('Y-m-01', strtotime($maxUpdateDateFullUser[0]['update_date']));
        $endDate = date('Y-m-t', strtotime($maxUpdateDateFullUser[0]['update_date']));
          
        $resultArrDataTotal = $scan_model->getScanTotalOnly($user_id,$startDate,$endDate);
       
        $data = [
            'maxUpdateDate' => $maxUpdateDate,
            'resultDataByu' => $resultArrDataTotal['total_byu'],
            'resultDataPerdana' => $resultArrDataTotal['total_perdana'],
            'resultDataTotal' => $resultArrDataTotal['total_scan'],
            'displayInputDate' => $displayInputDate,
            'resumeScan' => $scan_model->getScanSummaryCompareUser($varMaxDate, $user_id)
        ];

        return view('scan_summary_user_page', $data);
    }


}

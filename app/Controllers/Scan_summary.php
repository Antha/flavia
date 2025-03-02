<?php

namespace App\Controllers;
use App\Models\ScanHistoriesModel;
use DateTime;

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

    public function admin_report_bc()
    {
        // Cek jika user bukan admin, redirect ke halaman lain
        if (session()->get('user_level') !== 'admin') {
            return redirect()->to('/report/user_report');
        }

        $scan_model = new ScanHistoriesModel();

        // Cek apakah request datang dari AJAX
        if ($this->request->isAJAX()) {
            $periode = $this->request->getPost('periode_data') ?? date('Ym'); // Gunakan default jika kosong

            // Validasi input periode (jika ada)
            $validation = \Config\Services::validation();
            $validation->setRules([
                'periode_data' => 'required|min_length[6]|max_length[6]|numeric'
            ]);

            if (!$validation->run(['periode_data' => $periode])) {
                return $this->response->setJSON(['error' => 'Periode tidak valid!']);
            }

            
            if (!empty($periode)) {
                $validation = \Config\Services::validation();
                $rules = [
                    'periode_data' => 'required|max_length[6]',
                ];

                if (!$this->validate($rules)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => $validation->getErrors()
                    ]);
                }

                // Cek apakah tabel dengan periode tersebut ada
                $isTableExists = $scan_model->isTableExists($periode);
                if ($isTableExists == "0") {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'No Data Available In ' . $periode
                    ]);
                } else {
                    $displayInputDate = $periode;
                }
            } else {
                // Ambil data otomatis jika tidak ada periode yang dikirim
                $maxUpdateDateFull = $scan_model->getMaxUpdateDateFull();
                $lattestTabSoBarcode = $scan_model->getLattestSoBarcodeTable();
                $tableSoMaxPeriode = explode('sellout_barcode_', $lattestTabSoBarcode['TABLE_NAME']);

                if (!empty($maxUpdateDateFull[0]['datetime'])) {
                    $scanHistoryMaxPeriode = date('Ym', strtotime($maxUpdateDateFull[0]['datetime']));
                    $periodeUsed = (strtotime($scanHistoryMaxPeriode) > strtotime($tableSoMaxPeriode[1])) 
                        ? $tableSoMaxPeriode[1] 
                        : $scanHistoryMaxPeriode;
                    $displayInputDate = $periodeUsed;
                } else {
                    $displayInputDate = "0000-00-00";
                }
            }

            // Ambil data scan history berdasarkan periode
            $scanHistoryData = $scan_model->getScanHistoryDataAdmin($displayInputDate);

            // Return JSON response
            return $this->response->setJSON([
                'status' => 'success',
                'displayInputDate' => $displayInputDate,
                'resumeScan' => $scanHistoryData
            ]);
        }

        // Jika bukan AJAX, kembalikan halaman view biasa
        return view('scan_summary_admin_page');
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
            'resumeScan' => $scan_model->getScanSummaryCompareRealTimeUser($displayInputDate,$user_id,$startDate,$endDate)
        ];

        return view('scan_summary_user_page', $data);
        
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

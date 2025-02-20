<?php

namespace App\Controllers;
use App\Models\RewardModel;
use App\Models\ScanHistoriesModel;
use App\Models\UserHistoriesModel;

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

    public function admin_report()
    {
        // Cek jika user bukan admin, redirect ke halaman lain
        if (session()->get('user_level') !== 'admin') {
            return redirect()->to('/report/user_report');
        }

        $scan_model = new ScanHistoriesModel();

        // Cek apakah request datang dari AJAX
        if ($this->request->isAJAX()) {
            $periode = $this->request->getPost('periode_data');

            // Validasi input periode (jika ada)
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

    public function user_report()
    {
        if (session()->get('user_level') !== 'user') {
            return redirect()->to('/report/admin_report');
        }

        $scan_model = new ScanHistoriesModel();
        $user_id = $this->session_user->get('user_id');

        //if button submit clicked or not
        if($this->request->getPost('btn_submit_periode')){
            //validation
            $validation = \Config\Services::validation();
    
            // Define validation rules
            $rules = [
                'periode_data' => 'required|max_length[6]', // Example: must be 4-10 characters long
            ];

            if (!$this->validate($rules)) {
                // Validation failed
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }

            $periode = $this->request->getPost('periode_data');        
            $isTableExists = $scan_model->isTableExists($periode);
            if($isTableExists == "0"){
                return redirect()->back()->withInput()->with('errors', 'No Data Available In '.$periode);
            }else{
                $maxUpdateDate = date('F Y', strtotime($periode));
                $varMaxDate = date('Y-m-', strtotime($periode));
                $displayInputDate = $periode; 
            }
        }else{
            $maxUpdateDateFull = $scan_model->getMaxUpdateDateFull();
            if (!empty($maxUpdateDateFull[0]['datetime'])) {
                $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['datetime']));
                $varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['datetime']));
                $displayInputDate = date('Ym', strtotime($maxUpdateDateFull[0]['datetime'])); 
            } else {
                $maxUpdateDate = "No Data Found";
                $varMaxDate = "0000-00-";
                $displayInputDate = "202501";
            }
        }

        $resumeScan = $scan_model->getScanHistoryData($varMaxDate,$displayInputDate,$user_id);
        $resultArrDataTotal = $scan_model->getScanTotalOnly($varMaxDate,$displayInputDate,$user_id);

        $data = [
            'maxUpdateDate' => $maxUpdateDate,
            'resultDataByu' => $resultArrDataTotal['total_byu'],
            'resultDataPerdana' => $resultArrDataTotal['total_perdana'],
            'resultDataTotal' =>  $resultArrDataTotal['total_card'],
            'poinDataTotal' =>  $resultArrDataTotal['total_point'],
            'displayInputDate' => $displayInputDate,
            'resumeScan' => $scan_model->getScanHistoryData($varMaxDate,$displayInputDate,$user_id)
        ];

        return view('scan_summary_user_page', $data);
    }

}

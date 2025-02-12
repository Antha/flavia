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
        if (session()->get('user_level') !== 'admin') {
            return redirect()->to('/report/user_report');
        }

        $scan_model = new ScanHistoriesModel();
        //if button submit clicked or not
        if($this->request->getPost('btn_submit')){
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
            $branch = $this->request->getPost('filter_branch');
            $getCluster = $this->request->getPost('filer_cluster');

            if(!isset($getCluster)){
                $cluster = "";
            }else{
                $cluster = $getCluster;
            }
            
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

            $branch = '';
            $cluster = '';
        }

        $data = [
            'maxUpdateDate' => $maxUpdateDate,
            'displayInputDate' => $displayInputDate,
            'filterBranch' => $branch,
            'filterCluster' => $cluster,
            'resumeScan' => $scan_model->getScanHistoryDataAdmin($varMaxDate,$displayInputDate,$branch,$cluster)
        ];

        return view('scan_summary_admin_page', $data);
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

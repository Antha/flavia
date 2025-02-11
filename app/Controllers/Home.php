<?php

namespace App\Controllers;
use App\Models\ScanHistoriesModel;

use CodeIgniter\Email\Email;

class Home extends BaseController
{
    public function index(): string
    {
        $scan_model = new ScanHistoriesModel();
        $db = \Config\Database::connect();
        $builder = $db->table('scan_histories');

        $getMaxUpdateDate = $builder->selectMax('datetime')->get();
        $maxUpdateDateFull = $getMaxUpdateDate->getResultArray();

        if (!empty($maxUpdateDateFull[0]['datetime'])) {
            $maxUpdateDate = date('F Y', strtotime($maxUpdateDateFull[0]['datetime']));
            $varMaxDate = date('Y-m-', strtotime($maxUpdateDateFull[0]['datetime'])); 
        } else {
            $maxUpdateDate = "No Data Found";
            $varMaxDate = "0000-00-";
        }

        $queryDataByu = $builder->selectCount('msisdn')->where('card_type','byu')->like('datetime',$varMaxDate,'after')->get();
        $resultArrDataByu = $queryDataByu->getResultArray();

        if (!empty($resultArrDataByu) && $resultArrDataByu[0]['msisdn'] > 0) {
            $resultDataByu =  $resultArrDataByu[0]['msisdn'];
        } else {
            $resultDataByu = 0;
        }

        $queryDataPerdana = $builder->selectCount('msisdn')->where('card_type','perdana')->like('datetime',$varMaxDate,'after')->get();
        $resultArrDataPerdana = $queryDataPerdana->getResultArray();

        if (!empty($resultArrDataPerdana) && $resultArrDataPerdana[0]['msisdn'] > 0) {
            $resultDataPerdana =  $resultArrDataPerdana[0]['msisdn'];
        } else {
            $resultDataPerdana = 0;
        }

        $data['maxUpdateDate'] = $maxUpdateDate;
        $data['resultDataByu'] = $resultDataByu;
        $data['resultDataPerdana'] = $resultDataPerdana;
        $data['resultDataTotal'] = $resultDataByu + $resultDataPerdana;

        return view('home_page',$data);
    }

    public function sendEmail()
    {
        $email = \Config\Services::email();

        $to = $this->request->getPost('to');
        $subject = $this->request->getPost('subject');
        $message = $this->request->getPost('message');

        if (!$to || !$subject || !$message) {
            return $this->response->setJSON(['message'=>'Semua field harus diisi.'], 400);
        }

        $email->setFrom('admin@salesbalnus.com', 'Admin Sales Balnus');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) {
            return $this->response->setJSON(['message' => 'Email berhasil dikirim.'], 200);
        } else {
            // Menampilkan detail error
            $error = $email->printDebugger(['headers']);
            return $this->response->setJSON(['error' => 'Gagal mengirim email.', 'details' => $error], 500);
        }
    }
}
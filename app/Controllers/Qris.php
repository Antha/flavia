<?php

namespace App\Controllers;

use App\Models\ScanHistoriesModel;
use App\Models\ScanHistoriesJatengModel;
use App\Models\UpdateStocksJatengModel;

class Qris extends BaseController
{
    protected $scanHistoriesModel;
    protected $scanHistoriesJatengModel;
    protected $updateStocksJatengModel;
    protected $session_user;

    public function __construct()
    {
        $this->scanHistoriesModel = new ScanHistoriesModel();
        $this->scanHistoriesJatengModel = new ScanHistoriesJatengModel();
        $this->updateStocksJatengModel = new UpdateStocksJatengModel();
        $this->session_user = session();
    }

    public function index(): string
    {
        return view('qris_page');
    }

    public function scrape()
    {
        // URL target
        $url = $this->request->getVar('url'); // URL diambil dari parameter request

        if (!$url) {
            return $this->response->setJSON(['error' => 'URL is required'], 400);
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
                ]
            ]);
            
            $html = file_get_contents($url, false, $context);
            //echo $html;
            

            if (!$html) {
                return $this->response->setJSON(['error' => 'Failed to fetch content from the URL'], 500);
            }

            // Pola untuk mendeteksi angka Serial Number (contoh: 16 digit angka)
            $pattern = '/\b\d{16}\b/';
            // Cari semua angka yang cocok dengan pola
            if (preg_match($pattern, $html, $matches)) {
                $serialNumber = $matches[0]; // Serial number pertama yang ditemukan
            } else {
                return $this->response->setJSON(["error"=>"serial numbers not found"]);
            }

              // Pola regex untuk nomor HP Indonesia (dimulai dengan 08 dan panjang 10-14 digit)
            $pattern = '/\b08\d{8,12}\b/';
            // Cari semua nomor HP yang cocok
            if (preg_match_all($pattern, $html, $matches)) {
                $phoneNumbers = $matches[0]; // Ambil semua nomor HP yang ditemukan 
            }else {
                return $this->response->setJSON(["error"=>"phone numbers not found"]);
            }

            return $this->response->setJSON(['phone_numbers' => $phoneNumbers[0],'serial_numbers' => $serialNumber], 200);

        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()], 500);
        }
    }

    public function insertData(){
        try {
            $msisdn = $this->request->getPost('msisdn');
            $cardType = $this->request->getPost('cardType');
            $action = $this->request->getPost('action');
            $themodel =  $this->scanHistoriesModel;

            if( $this->session_user->get("region") && $this->session_user->get("region") == "JATENG-DIY"){
                if(!is_null($action) && $action == "update_stock"){
                    $themodel = $this->updateStocksJatengModel;
                }else{
                    $themodel = $this->scanHistoriesJatengModel;
                }
            }

            // Validasi jika msisdn kosong
            if (empty($msisdn)) {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "MSISDN is required"
                ])->setStatusCode(400);
            }

            $data = $themodel->getOneByMsisdn($msisdn);

            if (!empty($data)) {
                return $this->response->setJSON(['error'=>'Msisdn berikut sudah ada: ' . $msisdn])
                    ->setStatusCode(400);;
            }

            if(!is_null($action) && $action == "update_stock"){
                $themodel->insert([
                    "id_outlet" => $this->session_user->get("digipos_id"),
                    "nama_outlet" => $this->session_user->get("outlet_name"),
                    "city" => $this->session_user->get("city"),
                    "user_id" => $this->session_user->get("user_id"),
                    "cluster" => $this->session_user->get("cluster"),
                    "branch" => $this->session_user->get("branch"),
                    "regional" => $this->session_user->get("region"),
                    "msisdn" => $msisdn,
                    "card_type" => $cardType
                ]);
            }
            else{
                $themodel->insert([
                    "card_type" => $cardType,
                    "msisdn" => $msisdn,
                    "status" => "valid",
                    "user_id" => $this->session_user->get("user_id"),
                    //"fl_name" => $this->session_user->get("fl_name"),
                    //"digipos_id" => $this->session_user->get("digipos_id"),
                    //"outlet_name" => $this->session_user->get("outlet_name")
                ]);
            }

            return $this->response->setJSON([
                "status" => "success",
                "message" => "Data successfully inserted",
                "data" => [
                    "msisdn" => $msisdn,
                    "status" => "valid"
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Failed to insert data",
                "error" => $e->getMessage() // Menampilkan pesan error
            ])->setStatusCode(500);
        }
    }

}

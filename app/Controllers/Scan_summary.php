<?php

namespace App\Controllers;
use App\Models\RewardModel;

class Scan_summary extends BaseController
{
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

        $reward_model = new RewardModel();
        $db = \Config\Database::connect();
        $builder = $db->table('reward_item');

        // Get default reward update date
        $reward_update_date_default = $reward_model->getRewardUpdateDate() ?? date('Ym');

        // If filter is submitted, override default values
        if ($this->request->getPost('btn_submit_filter_reward')) {
            $reward_update_date_default = $this->request->getPost('periode_data_reward');
            $where_arr = array_filter([
                'periode' => $reward_update_date_default,
                'branch' => $this->request->getPost('filter_branch_reward'),
                'cluster' => $this->request->getPost('filter_cluster_reward')
            ]);
        } else {
            $where_arr = ['periode' => $reward_update_date_default];
        }

        // Fetch reward items
        $getAllRewardItem = $builder->where($where_arr)->get();
        $data = [
            'periode_data_reward_display' => $reward_update_date_default,
            'dataIsExists' => $getAllRewardItem->getNumRows() > 0 ? 'true' : 'false',
            'allRewardItem' => $getAllRewardItem->getResultArray()
        ];

        return view('scan_summary_admin_page', $data);
    }

    public function user_report()
    {
        if (session()->get('user_level') !== 'user') {
            return redirect()->to('/report/user_admin');
        }

        $reward_model = new RewardModel();
        $db = \Config\Database::connect();
        $builder = $db->table('reward_item');

        // Get default reward update date
        $reward_update_date_default = $reward_model->getRewardUpdateDate() ?? date('Ym');

        // If filter is submitted, override default values
        if ($this->request->getPost('btn_submit_filter_reward')) {
            $reward_update_date_default = $this->request->getPost('periode_data_reward');
            $where_arr = array_filter([
                'periode' => $reward_update_date_default,
                'branch' => $this->request->getPost('filter_branch_reward'),
                'cluster' => $this->request->getPost('filter_cluster_reward')
            ]);
        } else {
            $where_arr = ['periode' => $reward_update_date_default];
        }

        // Fetch reward items
        $getAllRewardItem = $builder->where($where_arr)->get();
        $data = [
            'periode_data_reward_display' => $reward_update_date_default,
            'dataIsExists' => $getAllRewardItem->getNumRows() > 0 ? 'true' : 'false',
            'allRewardItem' => $getAllRewardItem->getResultArray(),
            'cluster' => session()->get('cluster'),
            'branch' => session()->get('branch')
        ];

        return view('scan_summary_user_page', $data);
    }

}

<?php

namespace App\Controllers;

class Scan_summary extends BaseController
{
    public function index(): string
    {
        return view('scan_summary_page');
    }
}

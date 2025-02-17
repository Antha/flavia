<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CheckSession implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if session values are set and valid
        if (!session()->get("outlet_name") || 
            !session()->get("link_aja") || 
            !session()->get("digipos_id") || 
            !session()->get("idcard") || 
            session()->get("idcard") == 0) {
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No need for logic here, since we only want to filter before the controller
    }
}

?>
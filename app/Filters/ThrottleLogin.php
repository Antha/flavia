<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ThrottleLogin implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    protected $maxAttempts = 5; // Maksimal percobaan login
    protected $lockoutTime = 60; // Waktu blokir dalam detik (600 detik = 10 menit)

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $ip = $request->getIPAddress();
        $attemptsKey = "login_attempts_" . $ip;
        $lockoutKey = "lockout_time_" . $ip;

        $attempts = $session->get($attemptsKey) ?? 0;
        $lockoutTime = $session->get($lockoutKey);

        // Jika pengguna sudah diblokir, cek apakah waktu blokir sudah habis
        if ($lockoutTime && time() - $lockoutTime < $this->lockoutTime) {
            $remainingTime = ($this->lockoutTime - (time() - $lockoutTime)) / 60;

            $session->setFlashdata('lockout_message', 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . ceil($remainingTime) . ' menit.');
            return redirect()->to('/login'); // Redirect ke halaman login
            //return Services::response()
            //    ->setStatusCode(429) // Too Many Requests
            //    ->setBody("Terlalu banyak percobaan login. Silakan coba lagi dalam " . ceil($remainingTime) . " menit.");
        }

        // Jika sudah 5 kali gagal, set waktu blokir
        if ($attempts >= $this->maxAttempts) {
            $session->set($lockoutKey, time());
            $session->setFlashdata('lockout_message', 'Terlalu banyak percobaan login. Silakan coba lagi dalam 1 menit.');
            return redirect()->to('/login'); // Redirect ke halaman login
            //return Services::response()
            //    ->setStatusCode(429)
            //    ->setBody("Terlalu banyak percobaan login. Silakan coba lagi dalam 1 menit.");
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}

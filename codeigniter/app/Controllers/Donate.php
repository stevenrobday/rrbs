<?php

namespace App\Controllers;
use CodeIgniter\Controller;

class Donate extends Controller
{
    public function index()
    {
        if (strtolower($this->request->getMethod()) !== 'get') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $nav['session'] = session();

        echo view('templates/header');
        echo view('templates/navbar', $nav);
        echo view('users/donate');
        echo view('templates/modal');
        echo view('templates/footer', ['modal' => true]);
    }
}
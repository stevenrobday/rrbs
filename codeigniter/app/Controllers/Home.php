<?php namespace App\Controllers;
use App\Models\CookiesModel;
use App\Models\UserModel;

class Home extends BaseController
{
    public function index()
    {
        if (strtolower($this->request->getMethod()) !== 'get')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $session = session();
        $data['session'] = $session;
        echo view('templates/header');
        echo view('templates/navbar', $data);
        echo view('home/index');
        echo view('templates/modal');
        echo view('templates/footer', ['page' => 'index', 'modal' => true]);
    }
}

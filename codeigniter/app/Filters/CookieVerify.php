<?php

namespace App\Filters;

use App\Models\CookiesModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\I18n\Time;

class CookieVerify implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            if (isset($_COOKIE['id']) && isset($_COOKIE['cookiePassword']) && isset($_COOKIE['cookieSelector'])){
                $id = $_COOKIE['id'];
                $cookiePassword = $_COOKIE['cookiePassword'];
                $cookieSelector = $_COOKIE['cookieSelector'];

                $now = new Time('now', 'UTC', 'en_US');
                $now = $now->toDateTimeString();

                $cookiesModel = new CookiesModel();

                $rows = $cookiesModel->where('user_id', $id)->where('expiration_date >', $now)->findAll();

                foreach($rows as $row) {
                    if (password_verify($cookiePassword, $row['password_hash']) && password_verify($cookieSelector, $row['selector_hash'])) {
                        $userModel = new UserModel();
                        $data = $userModel->where('id', $id)->first();

                        if (isset($data)) {
                            $level = $data['level'] ? 'Admin' : 'User';

                            $ses_data = [
                                'id' => $id,
                                'username' => $data['username'],
                                'email' => $data['email'],
                                'level' => $level,
                                'isLoggedIn' => true,
                                'verified' => $data['verified'],
                                'invited' => $data['invited'],
                                'img' => $data['img']
                            ];

                            $session->set($ses_data);
                        }
                        break;
                    }
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}

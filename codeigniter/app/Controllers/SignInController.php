<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\CookiesModel;
use CodeIgniter\I18n\Time;


class SignInController extends Controller
{
    public function index()
    {
        if (strtolower($this->request->getMethod()) !== 'get')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        helper(['form']);

        echo view('templates/header');
        echo view('users/signIn');
        echo view('templates/footer');
    }

    public function loginAuth()
    {
        if (strtolower($this->request->getMethod()) !== 'post')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        helper(['form']);
        $session = session();

        $userModel = new UserModel();

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $data = $userModel->where('email', $email)->orWhere('username', $email)->first();

        if($data)
        {
            $pass = $data['password'];
            $authenticatePassword = password_verify($password, $pass);
            if($authenticatePassword)
            {
                $level = $data['level'] ? 'Admin' : 'User';

                $id = $data['id'];

                $sessionData = [
                    'id' => $id,
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'level' => $level,
                    'isLoggedIn' => true,
                    'verified' => $data['verified'],
                    'invited' => $data['invited'],
                    'img' => $data['img']
                ];

                $session->set($sessionData);

                $rememberMe = $this->request->getVar('rememberMe');

                if ($rememberMe === 'Yes') {
                    $cookiePassword = bin2hex(random_bytes(16));
                    $cookieSelector = bin2hex(random_bytes(32));

                    $domain = getenv('DOMAIN');

                    $options = array(
                        'expires' => time() + (86400 * 30),
                        'path' => '/',
                        'domain' => "{$domain}",
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Strict'
                    );

                    setcookie('id', $id, $options);
                    setcookie('cookiePassword', $cookiePassword, $options);
                    setcookie('cookieSelector', $cookieSelector, $options);

                    $hashedPassword = password_hash($cookiePassword, PASSWORD_DEFAULT);
                    $hashedSelector = password_hash($cookieSelector, PASSWORD_DEFAULT);

                    $expirationDate = new Time('+30 day', 'UTC', 'en_US');
                    $expirationDate = $expirationDate->toDateTimeString();

                    $cookieData = [
                        'user_id' => $id,
                        'password_hash' => $hashedPassword,
                        'selector_hash' => $hashedSelector,
                        'expiration_date' => $expirationDate
                    ];

                    $cookiesModel = new CookiesModel();

                    $cookiesModel->save($cookieData);
                }

                $path = $session->get('path');

                if ($path) return redirect()->to($path);

                return redirect()->to('/');
            }
            else{
                $session->setFlashdata('email', '');
                $session->setFlashdata('password', 'Your password is incorrect');
                echo view('templates/header');
                echo view('users/signIn');
                echo view('templates/footer');
            }
        }
        else{
            $session->setFlashdata('password', '');
            $session->setFlashdata('email', 'Username or email address is not registered');
            echo view('templates/header');
            echo view('users/signIn');
            echo view('templates/footer');
        }
    }

    public function signOut()
    {
        if (strtolower($this->request->getMethod()) !== 'get')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $session = session();
        $session->destroy();

        if (isset($_COOKIE['id']) && isset($_COOKIE['cookiePassword']) && isset($_COOKIE['cookieSelector'])) {
            $id = $_COOKIE['id'];
            $cookiePassword = $_COOKIE['cookiePassword'];
            $cookieSelector = $_COOKIE['cookieSelector'];

            $now = new Time('now', 'UTC', 'en_US');
            $now = $now->toDateTimeString();

            $cookiesModel = new CookiesModel();

            $rows = $cookiesModel->where('user_id', $id)->where('expiration_date >', $now)->findAll();

            foreach ($rows as $row) {
                if (password_verify($cookiePassword, $row['password_hash']) && password_verify($cookieSelector, $row['selector_hash'])) {
                    $cookiesModel->delete($row['id']);
                    break;
                }
            }
        }

        $domain = getenv('DOMAIN');

        $options = array(
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => "{$domain}",
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        );
        if (isset($_COOKIE['id'])) setcookie('id', '', $options);
        if (isset($_COOKIE['cookiePassword'])) setcookie('cookiePassword', '', $options);
        if (isset($_COOKIE['cookieSelector'])) setcookie('cookieSelector', '', $options);
        return redirect()->to('/signIn');
    }
}
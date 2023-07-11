<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\VerifyEmailModel;

class SignUpController extends Controller
{
    public function index()
    {
        if (strtolower($this->request->getMethod()) !== 'get')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        helper(['form']);
        echo view('templates/header');
        echo view('users/signUp');
        echo view('templates/footer');
    }

    public function store()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        helper(['form']);
        $rules = [
            'username' => 'required|min_length[3]|max_length[20]|is_unique[users.username]',
            'email' => 'required|min_length[7]|max_length[50]|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|max_length[30]',
            'confirmPassword' => 'matches[password]'
        ];

        $errors = [
            'username' => [
                'required' => 'Username is required',
                'min_length' => 'Username must be at least 3 characters',
                'max_length' => 'Username can\'t be over 20 characters',
                'is_unique' => 'Username is already registered',
            ],
            'email' => [
                'required' => 'Email is required',
                'min_length' => 'Email must be at least 7 characters',
                'max_length' => 'Email can\'t be over 50 characters',
                'valid' => 'Email address is invalid',
                'is_unique' => 'Email address is already registered',
            ],
            'password' => [
                'required' => 'Password is required',
                'min_length' => 'Password must be at least 8 characters',
                'max_length' => 'Password can\'t be over 30 characters',
            ],
            'confirmPassword' => [
                'matches' => 'Your passwords do not match',
            ]
        ];

        if ($this->validate($rules, $errors)) {
            $userModel = new UserModel();
            $username = $this->request->getVar('username');
            $userEmail = $this->request->getVar('email');

            $userData = [
                'username' => $username,
                'email' => $userEmail,
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
            ];

            $userId = $userModel->insert($userData);

            $session = session();

            $sessionData = [
                'id' => $userId,
                'username' => $username,
                'email' => $userEmail,
                'level' => "User",
                'isLoggedIn' => true,
                'verified' => 0,
                'invited' => 0,
                'img' => ''
            ];

            $session->set($sessionData);

            $token1 = bin2hex(random_bytes(16));
            $token2 = bin2hex(random_bytes(32));

            $token1Hash = password_hash($token1, PASSWORD_DEFAULT);
            $token2Hash = password_hash($token2, PASSWORD_DEFAULT);

            $verifyEmailData = [
                'user_id' => $userId,
                'token_1' => $token1Hash,
                'token_2' => $token2Hash
            ];

            $verifyEmailModel = new VerifyEmailModel();
            $verifyEmailModel->save($verifyEmailData);

            $email = \Config\Services::email();

            $domain = getenv('DOMAIN');

            $email->setFrom("noreply@{$domain}");
            $email->setTo($userEmail);
            $email->setSubject("RRBS.org Account Verification link");
            $link = "https://www.{$domain}/verifyEmail/$username/$token1/$token2";
            $body = "Welcome to RRBS.org! Click <a href='$link'>here</a> to verify your email address. If you did not make this request, you can safely ignore this email.";
            $email->setMessage($body);
            $body = "Welcome to RRBS.org! Go to $link to verify your email address. If you did not make this request, you can safely ignore this email.";
            $email->setAltMessage($body);
            $email->send();

            $data['success'] = true;
        } else $data['validation'] = $this->validator;

        echo view('templates/header');
        echo view('users/signUp', $data);
        echo view('templates/footer');
    }
}
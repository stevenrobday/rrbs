<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\ResetPasswordModel;
use CodeIgniter\I18n\Time;

class ResetPassword extends Controller
{
    public function index()
    {
        if (strtolower($this->request->getMethod()) !== 'get')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }
        helper(['form']);
        echo view('templates/header');
        echo view('users/requestResetPassword');
        echo view('templates/footer');
    }

    public function submit(){
        if (strtolower($this->request->getMethod()) !== 'post')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }
        helper(['form']);
        $session = session();

        $userModel = new UserModel();

        $email = $this->request->getVar('email');

        $data = $userModel->where('email', $email)->orWhere('username', $email)->first();

        if($data) {
            $token1 = bin2hex(random_bytes(16));
            $token2 = bin2hex(random_bytes(32));

            $token1Hash = password_hash($token1, PASSWORD_DEFAULT);
            $token2Hash = password_hash($token2, PASSWORD_DEFAULT);

            $expirationDate = new Time('+1 day', 'UTC', 'en_US');
            $expirationDate = $expirationDate->toDateTimeString();

            $resetPasswordData = [
                'user_id' => $data['id'],
                'token_1' => $token1Hash,
                'token_2' => $token2Hash,
                'expiration_date' => $expirationDate
            ];

            $resetPasswordModel = new ResetPasswordModel();
            $resetPasswordModel->save($resetPasswordData);

            $email = \Config\Services::email();

            $domain = getenv('DOMAIN');

            $email->setFrom("noreply@{$domain}");
            $email->setTo($data['email']);
            $email->setSubject("RRBS.org Password Reset link");
            $link = "https://www.{$domain}/resetPassword/{$data['username']}/$token1/$token2";
            $body = "Click <a href='$link'>here</a> to reset your password. This link will expire in 24 hours. If you did not make this request, you can safely ignore this email.";
            $email->setMessage($body);
            $body = "Go to $link to reset your password. This link will expire in 24 hours. If you did not make this request, you can safely ignore this email.";
            $email->setAltMessage($body);
            $email->send();

            $session->setFlashdata('email', '');
            $session->setFlashdata('success', 'Please check your email for a link to change your password.');
        }
        else {
            $session->setFlashdata('success', '');
            $session->setFlashdata('email', 'Username or email address is not registered');
        }
        echo view('templates/header');
        echo view('users/requestResetPassword');
        echo view('templates/footer');
    }

    public function resetPassword($username, $token1, $token2)
    {
        if (strtolower($this->request->getMethod()) !== 'get' && strtolower($this->request->getMethod()) !== 'post')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }
        helper(['form']);

        $userModel = new UserModel();

        $data = $userModel->where('username', $username)->first();

        if ($data) {
            $userId = $data['id'];
            $now = new Time('now', 'UTC', 'en_US');
            $now = $now->toDateTimeString();

            $resetPasswordModel = new ResetPasswordModel();

            $rows = $resetPasswordModel->where('user_id', $userId)->where('expiration_date >', $now)->findAll();

            $validated = false;

            foreach($rows as $row) {
                if (password_verify($token1, $row['token_1']) && password_verify($token2, $row['token_2'])) {
                    $id = $row['id'];
                    $validated = true;
                    break;
                }
            }

            if (!$validated) return $this->response->setStatusCode(403)->setBody('Unauthorized');

            $returnData = [
                'username' => $username,
                'token1' => $token1,
                'token2' => $token2
            ];

            if (strtolower($this->request->getMethod()) === 'post') {
                $rules = [
                    'password' => 'required|min_length[8]|max_length[30]',
                    'confirmPassword' => 'matches[password]'
                ];

                $errors = [
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
                    $returnData['success'] = true;
                    $resetPasswordModel->delete($id);
                    $userModel->update($userId, ['password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)]);
                } else $returnData['validation'] = $this->validator;
            }

            echo view('templates/header');
            echo view('users/resetPassword', $returnData);
            echo view('templates/footer', ['page' => 'resetPassword']);
        } else return $this->response->setStatusCode(403)->setBody('Unauthorized');
    }
}
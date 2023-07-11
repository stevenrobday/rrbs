<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\VerifyEmailModel;

class VerifyEmail extends Controller
{
    public function index($username, $token1, $token2)
    {
        if (strtolower($this->request->getMethod()) !== 'get')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $userModel = new UserModel();

        $data = $userModel->where('username', $username)->first();

        if ($data) {
            $userId = $data['id'];

            $verifyEmailModel = new VerifyEmailModel();

            $rows = $verifyEmailModel->where('user_id', $userId)->findAll();

            $validated = false;

            foreach($rows as $row) {
                if (password_verify($token1, $row['token_1']) && password_verify($token2, $row['token_2'])) {
                    $verifyEmailModel->delete($row['id']);
                    $userModel->update($userId, ['verified' => 1]);
                    $validated = true;
                    break;
                }
            }

            if (!$validated) return $this->response->setStatusCode(403)->setBody('Unauthorized');

            $session = session();
            $session->set('verified', 1);

            echo view('templates/header');
            echo view('users/verifyEmail', ['success' => true]);
            echo view('templates/footer', ['page' => 'verifyEmail']);
        } else return $this->response->setStatusCode(403)->setBody('Unauthorized');
    }
}

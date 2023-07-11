<?php namespace App\Controllers;

use App\Models\UserModel;

class Invite extends BaseController
{
    public function getUsers($invited = 0)
    {
        if (strtolower($this->request->getMethod()) !== 'get')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $invited = intval($invited);
        $model = new UserModel();
        $users = $model->where('level', 0)->where('invited', (int) $invited)->orderBy('username', 'asc')->findAll();

        $session = session();
        $nav['session'] = $session;
        $data = [
            'invited' => $invited,
            'users' => $users
        ];

        echo view('templates/header');
        echo view('templates/navbar', $nav);
        echo view('admin/invite', $data);
        echo view('templates/modal');
        echo view('templates/footer', ['page' => 'invite', 'modal' => true]);
    }

    public function inviteUser()
    {
        if (strtolower($this->request->getMethod()) !== 'patch')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $csrfField = csrf_hash();

        $id = (int) $this->request->getVar('id');
        $invited = (int) $this->request->getVar('invited');

        $model = new UserModel();

        try {
            $model->update($id, ['invited' => $invited]);
        } catch (\Exception $e) {
            return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
        }

        if ($invited) {
            $user = $model->where('id', $id)->first();
            $session = session();
            $admin = $model->where('id', $session->get('id'))->first();
            $domain = getenv('DOMAIN');

            $email = \Config\Services::email();

            $email->setFrom("noreply@{$domain}");
            $email->setTo($user['email']);
            $email->setSubject('You have been invited to suggest videos at RRBS.org!');
            $body = "{$admin['username']} has invited you to suggest videos. Click <a href='https://www.{$domain}/suggestVideos'>here</a> to get started!";
            $email->setMessage($body);
            $body = "{$admin['username']} has invited you to suggest videos. Go to https://www.{$domain}/suggestVideos to get started!";
            $email->setAltMessage($body);
            $email->send();
        }

        return json_encode(['success' => 'success']);
    }
}
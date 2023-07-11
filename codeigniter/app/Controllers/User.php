<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class User extends Controller
{
    public function addImage()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $session = session();
        $user_id = $session->get('id');
        $csrfField = csrf_hash();
        $image = $this->request->getVar('image');

        $img_arr_a = explode(";", $image);
        $img_arr_b = explode(",", $img_arr_a[1]);

        $data = base64_decode($img_arr_b[1]);
        $img_name = time() . "_{$user_id}.png";

        file_put_contents("assets/img/profile/$img_name", $data);

        $model = new UserModel();

        try {
            $model->update($user_id, ['img' => $img_name]);
        } catch (\Exception $e) {
            return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
        }

        $data = $model->where('id', $user_id)->first();

        $level = $data['level'] ? 'Admin' : 'User';

        $ses_data = [
            'id' => $user_id,
            'username' => $data['username'],
            'email' => $data['email'],
            'level' => $level,
            'isLoggedIn' => true,
            'verified' => $data['verified'],
            'invited' => $data['invited'],
            'img' => $data['img']
        ];

        $session->set($ses_data);

        return json_encode(['success' => ['img' => $img_name, 'csrfField' => $csrfField]]);
    }

    public function addAbout()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $session = session();
        $user_id = $session->get('id');
        $csrfField = csrf_hash();
        $about = $this->request->getVar('about');

        $model = new UserModel();

        try {
            $model->update($user_id, ['about' => $about]);
        } catch (\Exception $e) {
            return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
        }

        return json_encode(['success' => ['csrfField' => $csrfField]]);
    }
}
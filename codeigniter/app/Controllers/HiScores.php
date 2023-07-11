<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\SuggestedVideoModel;


class HiScores extends Controller
{
    public function getScores()
    {
        if (strtolower($this->request->getMethod()) !== 'get') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $userModel = new UserModel();
        $builder = $userModel->builder();
        $builder->select('users.id, COUNT(CASE WHEN status_id = 2 THEN 1 END) AS count, username, img, about');
        $builder->where('invited', 1);
        $builder->join('suggested_videos', 'users.id = user_id', 'left');
        $builder->orderBy('count DESC, username ASC');
        $builder->groupBy('user_id');

        $rows = $builder->get()->getResultArray();

        $suggestedModel = new SuggestedVideoModel();

        foreach($rows as &$row) {
            $count = intval($row['count']);

            if ($count === 0) $level = 0;
            elseif ($count <= 5) $level = 1;
            elseif ($count <= 11) $level = 2;
            elseif ($count <= 24) $level = 3;
            elseif ($count <= 49) $level = 4;
            elseif ($count <= 99) $level = 5;
            elseif ($count <= 150) $level = 6;
            elseif ($count <= 199) $level = 7;
            elseif ($count <= 249) $level = 8;
            elseif ($count <= 299) $level = 9;
            elseif ($count <= 499) $level = 10;
            else $level = 11;

            if ($count > 0) {
                $row['videos'] = $suggestedModel->select('video_id, title, thumbnail')
                    ->where('user_id', $row['id'])
                    ->where('status_id', 2)
                    ->orderBy('title', 'asc')
                    ->findAll();
            }

            $row['level'] = $level;
        }

        $session = session();
        $nav['session'] = $session;

        echo view('templates/header');
        echo view('templates/navbar', $nav);
        echo view('users/hiScores', ['scores' => $rows]);
        echo view('templates/modal');
        echo view('templates/footer', ['modal' => true]);
    }
}
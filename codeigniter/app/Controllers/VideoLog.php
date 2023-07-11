<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\VideoLogModel;
use CodeIgniter\I18n\Time;

class VideoLog extends Controller
{
    public function getVideoLog($timezone = 'UTC')
    {
        $now = new Time('now', 'UTC', 'en_US');
        $startTime = $now->subHours(72);
        $startTime = $startTime->toDateTimeString();

        $model = new VideoLogModel();

        $videos = $model->select('video_id, title, thumbnail, datetime')
            ->where('datetime >=', $startTime)
            ->join('videos v', 'videos_id = v.id', 'left')
            ->orderBy('datetime', 'desc')
            ->findAll();

        $timezone = str_replace('-', '/', $timezone);

        foreach ($videos as &$video)
        {
            try {
                $utc = Time::parse($video['datetime'], 'UTC');
            } catch (\Exception $e) {
                exit($e->getMessage());
            }
            try {
                $converted = $utc->setTimezone($timezone);
            } catch (\Exception $e) {
                exit($e->getMessage());
            }
            $video['datetime'] = $converted->format('m/d/Y h:i:s A');
        }

        $session = session();
        $nav['session'] = $session;
        $data['videos'] = $videos;

        echo view('templates/header');
        echo view('templates/navbar', $nav);
        echo view('users/videoLog', $data);
        echo view('templates/modal');
        echo view('templates/footer', ['modal' => true]);
    }
}
<?php namespace App\Controllers;

use App\Models\ScheduleModel;
use App\Models\VideoModel;

class Categories extends BaseController
{
    public function getCategories($scheduleId = 0)
    {
        if (strtolower($this->request->getMethod()) !== 'get') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $videoModel = new VideoModel();
        $scheduleModel = new ScheduleModel();
        $videos = $videoModel->where('schedule_id', (int) $scheduleId)->where('deleted', 0)->orderBy('title', 'asc')->findAll();
        $schedules = $scheduleModel->orderBy('category', 'asc')->findAll();

        foreach ($schedules as &$schedule) {
            $schedule['startTime'] = $this->getReadableHour($schedule['start_time']);
            $schedule['endTime'] = $this->getReadableHour($schedule['end_time']);
        }

        $session = session();
        $nav['session'] = $session;
        $data = [
            'videos' => $videos,
            'schedules' => $schedules,
            'scheduleId' => $scheduleId
        ];

        echo view('templates/header');
        echo view('templates/navbar', $nav);
        echo view('admin/categories', $data);
        echo view('templates/modal');
        echo view('templates/footer', ['page' => 'categories', 'modal' => true]);
    }
}
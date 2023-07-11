<?php namespace App\Controllers;

use App\Models\NowPlayingModel;
use App\Models\ScheduleModel;
use App\Models\UserModel;
use App\Models\VideoModel;
use App\Models\SuggestedVideoModel;
use App\Models\StatusModel;
use CodeIgniter\I18n\Time;

class Video extends BaseController
{
    public function addVideo($level)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $csrfField = csrf_hash();

        $start_time = $this->request->getVar('start_time');
        $end_time = $this->request->getVar('end_time');

        $regex = $this->checkRegexTimes($start_time, $end_time);
        if ($regex['bool']) return json_encode(['error' => ['errorMsg' => $regex['error'], 'csrfField' => $csrfField]]);
        else {
            $start_time = $regex['start_time'];
            $end_time = $regex['end_time'];
        }

        $start_time = $start_time ? $this->convertTimeToSeconds($start_time) : 0;
        $end_time = $end_time ? $this->convertTimeToSeconds($end_time) : 0;

        if ($start_time && $end_time)
            if ($start_time > $end_time)
                return json_encode(['error' => ['errorMsg' => "Start time cannot be greater than end time", 'csrfField' => $csrfField]]);

        $videoId = $this->request->getVar('videoId');
        $ch = curl_init();
        $curlVideoId = curl_escape($ch, $videoId);
        curl_close($ch);
        $body = $this->getVideoData($curlVideoId);
        $region = array();

        foreach ($body['items'] as $item) {
            $vTime = $item['contentDetails']['duration'];
            $title = $item['snippet']['title'];
            $thumbnail = $item['snippet']['thumbnails']['medium']['url'];
            $embed = $item['status']['embeddable'];
            if (isset($item['contentDetails']['regionRestriction']['blocked'])) $region = $item['contentDetails']['regionRestriction']['blocked'];
        }

        if (!isset($vTime) || !isset($title) || !isset($thumbnail) || !isset($embed)) return json_encode(['error' => ['errorMsg' => 'Invalid video link', 'csrfField' => $csrfField]]);
        if (!$embed) return json_encode(['error' => ['errorMsg' => 'Playback on other websites has been disabled by the video owner', 'csrfField' => $csrfField]]);

        if (count($region)) {
            $flip = array_flip($region);
            if (isset($flip['US'])) return json_encode(['error' => ['errorMsg' => 'Video is unavailable in US', 'csrfField' => $csrfField]]);
        }

        $time = $this->convertTime($vTime);

        if ($start_time > $time)
            return json_encode(['error' => ['errorMsg' => "Start time is greater than video length", 'csrfField' => $csrfField]]);
        if ($end_time > $time)
            return json_encode(['error' => ['errorMsg' => "End time is greater than video length", 'csrfField' => $csrfField]]);

        $model = new VideoModel();
        $videos = $model->where('video_id', $videoId)->where('deleted', 0)->findAll();

        $isDuplicate = $this->checkForDuplicateVideos($videos, $start_time, $end_time, $time, false);
        if ($isDuplicate['bool']) return json_encode(['error' => ['errorMsg' => $isDuplicate['error'], 'csrfField' => $csrfField]]);

        if ($level === 'user') {
            $suggestedVideoModel = new SuggestedVideoModel();
            $videos = $suggestedVideoModel->where('video_id', $videoId)->where('status_id', 1)->findAll();

            $isDuplicate = $this->checkForDuplicateVideos($videos, $start_time, $end_time, $time,true);

            if ($isDuplicate['bool']) return json_encode(['error' => ['errorMsg' => $isDuplicate['error'], 'csrfField' => $csrfField]]);
        }

        $submitToken = (int) $this->request->getVar('submitToken');

        if ($time >= 300) {
            if (!$submitToken && !$start_time && !$end_time) return json_encode(['submitToken' => ['token' => 1, 'csrfField' => $csrfField]]);
        }

        $data = [
            'video_id' => $videoId,
            'video_length' => $time,
            'title' => $title,
            'thumbnail' => $thumbnail
        ];

        if ($start_time) $data['start'] = $start_time;
        if ($end_time) $data['end'] = $end_time;

        if ($level === 'admin') {
            $data['schedule_id'] = (int) $this->request->getVar('scheduleId');
            try {
                $model->insert($data);
            } catch (\Exception $e) {
                return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
            }
        }
        elseif ($level === 'user') {
            $session = session();
            $data['user_id'] = $session->get('id');
            try {
                $suggestedVideoModel->insert($data);
            } catch (\Exception $e) {
                return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
            }

            $userModel = new UserModel();
            $user = $userModel->find($data['user_id']);
            $username = $user['username'];

            $email = \Config\Services::email();

            $domain = getenv('DOMAIN');

            $email->setFrom("noreply@{$domain}");
            $email->setTo('mench@slovenly.com');
            $email->setSubject("$username has suggested a new video!");
            $body = "$username has suggested the video <a href='https://www.youtube.com/watch?v=$videoId'>$title</a>! Click <a href='https://www.{$domain}/suggestedVideos'>here</a> to approve or deny it.";
            $email->setMessage($body);
            $body = "$username has suggested the video '$title' located at https://www.youtube.com/watch?v=$videoId . Go to https://www.{$domain}/suggestedVideos to approve or deny it.";
            $email->setAltMessage($body);
            $email->send();
        }

        return json_encode(['success' => 'success']);
    }

    public function updateSchedule()
    {
        if (strtolower($this->request->getMethod()) !== 'patch')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $csrfField = csrf_hash();

        $model = new VideoModel();
        $videoId = (int) $this->request->getVar('videoId');
        $scheduleId = (int) $this->request->getVar('scheduleId');
        try {
            $model->update($videoId, ['schedule_id' => $scheduleId]);
        } catch (\Exception $e) {
            return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
        }
        return json_encode(['success' => ['csrfField' => $csrfField]]);
    }

    public function updateTimes()
    {
        if (strtolower($this->request->getMethod()) !== 'patch')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $csrfField = csrf_hash();

        $videoId = (int) $this->request->getVar('videoId');
        $start_time = $this->request->getVar('start_time');
        $end_time = $this->request->getVar('end_time');

        $regex = $this->checkRegexTimes($start_time, $end_time);
        if ($regex['bool']) return json_encode(['error' => ['errorMsg' => $regex['error'], 'csrfField' => $csrfField]]);
        else {
            $start_time = $regex['start_time'];
            $end_time = $regex['end_time'];
        }

        $start_time = $start_time ? $this->convertTimeToSeconds($start_time) : 0;
        $end_time = $end_time ? $this->convertTimeToSeconds($end_time) : 0;

        if ($start_time && $end_time)
            if ($start_time > $end_time)
                return json_encode(['error' => ['errorMsg' => "Start time cannot be greater than end time", 'csrfField' => $csrfField]]);

        $model = new VideoModel();
        $video = $model->find($videoId);
        $time = $video['video_length'];

        if ($start_time > $time)
            return json_encode(['error' => ['errorMsg' => "Start time is greater than video length", 'csrfField' => $csrfField]]);
        if ($end_time > $time)
            return json_encode(['error' => ['errorMsg' => "End time is greater than video length", 'csrfField' => $csrfField]]);

        $videos = $model->where('id !=', $video['id'])->where('video_id', $video['video_id'])->where('deleted', 0)->findAll();

        $isDuplicate = $this->checkForDuplicateVideos($videos, $start_time, $end_time, $time, false);
        if ($isDuplicate['bool']) return json_encode(['error' => ['errorMsg' => $isDuplicate['error'], 'csrfField' => $csrfField]]);

        try {
            $model->update($videoId, ['start' => $start_time, 'end' => $end_time]);
        } catch (\Exception $e) {
            return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
        }
        return json_encode(['success' => ['csrfField' => $csrfField]]);
    }

    public function getVideos($search = '')
    {
        if (strtolower($this->request->getMethod()) !== 'get')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }

        $videos = $this->searchVideos($search);

        $scheduleModel = new ScheduleModel();

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
            'search' => $search
        ];

        echo view('templates/header');
        echo view('templates/navbar', $nav);
        echo view('admin/videos', $data);
        echo view('templates/modal');
        echo view('templates/footer', ['page' => 'videos', 'modal' => true]);
    }

    public function searchVideosUser($search = '')
    {
        if (strtolower($this->request->getMethod()) !== 'get')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }
        $videos = $this->searchVideos($search);
        return json_encode(['success' => $videos]);
    }

    private function searchVideos($search): array
    {
        $videoModel = new VideoModel();
        if (strlen($search))
        {
            $searchArray = explode(' ', $search);

            $builder = $videoModel->builder();

            $builder->where('deleted', 0);

            foreach($searchArray as $value) {
                $builder->like('title', $value, 'both', true, true);
            }

            $builder->orderBy('title', 'asc');
            $videos = $builder->get()->getResultArray();
        }
        else $videos = $videoModel->where('deleted', 0)->orderBy('title', 'asc')->findAll();

        foreach($videos as &$video) {
            $video['start_time'] = $video['start'] ? $this->convertSecondsToTime($video['start']) : 0;
            $video['end_time'] = $video['end'] ? $this->convertSecondsToTime($video['end']) : 0;
        }

        return $videos;
    }

    public function deleteVideo()
    {
        if (strtolower($this->request->getMethod()) !== 'patch')  {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }
        $csrfField = csrf_hash();
        $model = new VideoModel();
        $id = (int) $this->request->getVar('id');
        try {
            $model->update($id, ['deleted' => 1]);
        } catch (\Exception $e) {
            return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
        }
        return json_encode(['success' => 'success']);
    }

    public function getSuggestedUserVideos()
    {
        if (strtolower($this->request->getMethod()) !== 'get') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }
        $session = session();
        $model = new SuggestedVideoModel();
        $videos = $model->select('video_id, title, thumbnail, start, end, username, status, comments')
            ->where('user_id', $session->get('id'))
            ->join('users', 'approved_by = users.id', 'left')
            ->join('statuses', 'status_id = statuses.id')
            ->orderBy('title', 'asc')
            ->findAll();

        foreach($videos as &$video) {
            $video['start_time'] = $video['start'] ? $this->convertSecondsToTime($video['start']) : 0;
            $video['end_time'] = $video['end'] ? $this->convertSecondsToTime($video['end']) : 0;
        }

        $nav['session'] = $session;
        $data = [
            'videos' => $videos
        ];

        echo view('templates/header');
        echo view('templates/navbar', $nav);
        echo view('user/suggestVideos', $data);
        echo view('templates/modal');
        echo view('templates/footer', ['page' => 'suggestVideos', 'modal' => true]);
    }

    public function getSuggestedAdminVideos($statusId = 1)
    {
        if (strtolower($this->request->getMethod()) !== 'get') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }
        $statusId = (int) $statusId;
        $session = session();
        $model = new SuggestedVideoModel();
        $videos = $model->select('suggested_videos.id, video_id, title, thumbnail, U1.username AS approved_by_username, U2.username AS submitted_by_username, U2.email AS submitted_by_email, status, schedule_id, start, end, comments')
            ->where('status_id', $statusId)
            ->join('users U1', 'approved_by = U1.id', 'left')
            ->join('users U2', 'user_id = U2.id')
            ->join('statuses', 'status_id = statuses.id')
            ->orderBy('submitted_by_username', 'asc')
            ->orderBy('title', 'asc')
            ->findAll();

        foreach($videos as &$video) {
            $video['start_time'] = $video['start'] ? $this->convertSecondsToTime($video['start']) : 0;
            $video['end_time'] = $video['end'] ? $this->convertSecondsToTime($video['end']) : 0;
        }

        $statusModel = new StatusModel();
        $statuses = $statusModel->orderBy('id', 'asc')->findAll();

        $scheduleModel = new ScheduleModel();
        $schedules = $scheduleModel->orderBy('category', 'asc')->findAll();

        foreach ($schedules as &$schedule) {
            $schedule['startTime'] = $this->getReadableHour($schedule['start_time']);
            $schedule['endTime'] = $this->getReadableHour($schedule['end_time']);
        }

        $nav['session'] = $session;
        $data = [
            'statusId' => $statusId,
            'statuses' => $statuses,
            'videos' => $videos,
            'schedules' => $schedules
        ];

        echo view('templates/header');
        echo view('templates/navbar', $nav);
        echo view('admin/suggestedVideos', $data);
        echo view('templates/modal');
        echo view('templates/footer', ['page' => 'suggestedVideos', 'modal' => true]);
    }

    public function updateSuggestedVideo()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setBody('Method Not Allowed');
        }
        $csrfField = csrf_hash();
        $id = (int) $this->request->getPost('id');
        $session = session();
        $approvedById = $session->get('id');
        $statusId = (int) $this->request->getPost('statusId');
        $scheduleId = (int) $this->request->getPost('scheduleId');
        $status = $this->request->getPost('status');
        $comments = $this->request->getPost('comments');
        $start = $this->request->getPost('start');
        $end = $this->request->getPost('end');

        $regex = $this->checkRegexTimes($start, $end);
        if ($regex['bool']) return json_encode(['error' => ['errorMsg' => $regex['error'], 'csrfField' => $csrfField]]);
        else {
            $start = $regex['start_time'];
            $end = $regex['end_time'];
        }

        $start = $start ? $this->convertTimeToSeconds($start) : 0;
        $end = $end ? $this->convertTimeToSeconds($end) : 0;

        if ($start && $end)
            if ($start > $end)
                return json_encode(['error' => ['errorMsg' => "Start time cannot be greater than end time", 'csrfField' => $csrfField]]);

        $model = new SuggestedVideoModel();
        $video = $model->find($id);
        $videoLength = intval($video['video_length']);

        if ($start > $videoLength)
            return json_encode(['error' => ['errorMsg' => "Start time is greater than video length", 'csrfField' => $csrfField]]);
        if ($end > $videoLength)
            return json_encode(['error' => ['errorMsg' => "End time is greater than video length", 'csrfField' => $csrfField]]);

        $videoModel = new VideoModel();
        $videoId = $video['video_id'];
        $videos = $videoModel->where('video_id', $videoId)->where('deleted', 0)->findAll();

        $isDuplicate = $this->checkForDuplicateVideos($videos, $start, $end, $videoLength, false);

        if ($isDuplicate['bool']) {
            $statusId = 3;
            $status = 'denied';
            $comments = $isDuplicate['error'];
        }

        $data = [
            'approved_by' => $approvedById,
            'status_id' => $statusId,
            'schedule_id' => $scheduleId,
            'comments' => $comments,
            'start' => $start,
            'end' => $end
        ];
        try {
            $model->update($id, $data);
        } catch (\Exception $e) {
            return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
        }

        if ($statusId === 2) {
            $title = $video['title'];
            $data = [
                'video_id' => $videoId,
                'video_length' => $videoLength,
                'title' => $title,
                'thumbnail' => $video['thumbnail'],
                'schedule_id' => $scheduleId,
                'start' => $start,
                'end' => $end
            ];
            try {
                $videoModel->insert($data);
            } catch (\Exception $e) {
                return json_encode(['error' => ['errorMsg' => $e->getMessage(), 'csrfField' => $csrfField]]);
            }

            $userModel = new UserModel();
            $user = $userModel->find($video['user_id']);
            $admin = $userModel->find($approvedById);
            $email = \Config\Services::email();
            $domain = getenv('DOMAIN');

            $email->setFrom("noreply@{$domain}");
            $email->setTo($user['email']);
            $email->setSubject("Your video has been $status");
            $username = $admin['username'];
            $body = "Your video '$title' has been $status by $username";
            if (strlen($comments)) $body .= " with the following comments: <br/><br/>$comments";
            $email->setMessage($body);
            $body = "Your video '$title' has been $status by $username";
            if (strlen($comments)) $body .= " with the following comments: \r\n$comments";
            $email->setAltMessage($body);
            $email->send();
        }

        return json_encode(['success' => $id]);
    }

    public function playVideo()
    {
        $playingModel = new NowPlayingModel();

        $nowPlaying = $playingModel->join('videos', 'videos.id = videos_id')->find(1);

        $startTime = new Time($nowPlaying['start_time'], 'UTC', 'en_US');
        $now = new Time('now', 'UTC', 'en_US');
        $difference = $startTime->difference($now);

        $seconds = $difference->getSeconds();
        $start = intval($nowPlaying['start']);
        $end = intval($nowPlaying['end']);

        if ($start && $end) return json_encode(['videoId'=> $nowPlaying['video_id'], 's' => $start + $seconds, 'e' => $end]);
        if ($start) return json_encode(['videoId'=> $nowPlaying['video_id'], 's' => $start + $seconds]);
        if ($end) return json_encode(['videoId'=> $nowPlaying['video_id'], 's' => $seconds, 'e' => $end]);

        return json_encode(['videoId'=> $nowPlaying['video_id'], 's' => $seconds]);
    }

    private function checkForDuplicateVideos($videos, $start_time, $end_time, $time, $suggested): array
    {
        if (count($videos)) {
            if (!$start_time && !$end_time) {
                if (!$suggested) return array('bool' => true, 'error' => 'Video has already been uploaded');
                else return array('bool' => true, 'error' => 'Video has already been suggested');
            }
            else {
                if (!$end_time) $reqTimeArray = array_flip(range($start_time, $time));
                else $reqTimeArray = array_flip(range($start_time, $end_time));

                $videosTimeArray = array();
                foreach ($videos as $video) {
                    if (!$video['start'] && !$video['end']) {
                        if (!$suggested) return array('bool' => true, 'error' => 'Video has already been uploaded');
                        else return array('bool' => true, 'error' => 'Video has already been suggested');
                    }
                    elseif (!$video['end']) $videosTimeArray[] = range(intval($video['start']), intval($video['video_length']));
                    else $videosTimeArray[] = range(intval($video['start']), intval($video['end']));
                }

                $count = count($videosTimeArray);

                for ($i = 0; $i < $count; $i++) {
                    foreach ($videosTimeArray[$i] as $v) {
                        if (isset($reqTimeArray[$v]))
                        {
                            if (!$suggested) return array('bool' => true, 'error' => 'Video with overlapping times has already been uploaded');
                            else return array('bool' => true, 'error' => 'Video with overlapping times has already been suggested');
                        }
                    }
                }
            }
        }
        return array('bool' => false);
    }

    private function convertTime($yt): int
    {
        $yt=str_replace(['P','T'],'',$yt);
        foreach(['D','H','M','S'] as $a){
            $pos=strpos($yt,$a);
            if($pos!==false) ${$a}=substr($yt,0,$pos); else { ${$a}=0; continue; }
            $yt=substr($yt,$pos+1);
        }
        return (($D*24*3600)+($H*3600)+($M*60)+$S);
    }

    private function checkRegexTimes($start_time, $end_time): array
    {
        $start_time = $this->regexTime($start_time);
        $end_time = $this->regexTime($end_time);

        if ($start_time === false && $end_time === false)
            return array('bool' => true, 'error' => "Start and end times are not in the correct format");
        elseif ($start_time === false)
            return array('bool' => true, 'error' => "Start time is not in the correct format");
        elseif ($end_time === false)
            return array('bool' => true, 'error' => "End time is not in the correct format");
        return array('bool' => false, 'start_time' => $start_time, 'end_time' => $end_time);
    }

    private function regexTime($time)
    {
        $colonStart = "/^:/";
        $time = preg_replace($colonStart, '', $time);
        $hoursMinutesSeconds = "/^\d+:\d+:\d+$/";
        $minutesSeconds = "/^\d+:\d+$/";
        $seconds = "/^\d+$/";
        if (preg_match($hoursMinutesSeconds, $time) || preg_match($minutesSeconds, $time) || preg_match($seconds, $time)) return $time;
        return false;
    }

    private function convertTimeToSeconds($t): int
    {
        sscanf($t, "%d:%d:%d", $hours, $minutes, $seconds);
        if (isset($seconds)) $s = $hours * 3600 + $minutes * 60 + $seconds;
        elseif (isset($minutes)) $s = $hours * 60 + $minutes;
        else $s = $hours;

        return $s;
    }

    private function convertSecondsToTime($s): string
    {
        return sprintf('%02d:%02d:%02d', (intdiv($s,3600)),(intdiv($s,60)%60), $s%60);
    }
}
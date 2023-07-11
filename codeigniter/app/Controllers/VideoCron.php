<?php namespace App\Controllers;

use App\Models\NowPlayingModel;
use App\Models\ScheduleModel;
use App\Models\VideoModel;
use App\Models\VideoLogModel;
use App\Models\PromoCountModel;
use App\Models\QueuedVideoModel;
use App\Models\CurrentTimezoneModel;
use CodeIgniter\I18n\Time;

class VideoCron extends BaseController
{
    public function run($sleep)
    {
        if (!$this->request->isCLI()) die;

        sleep($sleep);

        $playingModel = new NowPlayingModel();

        $nowPlaying = $playingModel->find(1);

        if (!isset($nowPlaying)) $this->queue($playingModel);
        else
        {
            $endTime = new Time($nowPlaying['end_time'], 'UTC', 'en_US');
            $now = new Time('now', 'UTC', 'en_US');
            $difference = $now->difference($endTime);
            $seconds = $difference->getSeconds();
            if ($seconds > 0) sleep($seconds);
            try {
                $playingModel->delete(1);
            } catch (\Exception $e) {
                $this->emailError($e->getMessage());
            }
            $this->queue($playingModel);
        }
    }

    private function queue($playingModel)
    {
        $promoModel = new PromoCountModel();
        $videoModel = new VideoModel();
        $queuedModel = new QueuedVideoModel();
        $promoRow = $promoModel->find(1);
        $queuedRow = $queuedModel->find(1);
        $promoCount = intval($promoRow['count']);

        if ($promoCount >= 5) {
            $promoCount = 0;
            $video = $videoModel->find(155);
            $this->setVideo($video, $videoModel);
        }
        elseif (!isset($queuedRow)) {
            $promoCount++;
            $this->getVideo($videoModel);
        }
        else {
            $queuedModel->delete(1);
            $promoCount++;
            $video = $videoModel->find($queuedRow['videos_id']);
//            $nowScheduledModel = new NowScheduledModel();
//            $nowScheduled = $nowScheduledModel->find(1);
//            $scheduleId = isset($nowScheduled) ? intval($nowScheduled['schedule_id']) : 0;
            $now = new Time('now', 'UTC', 'en_US');
            $currentTimezoneModel = new CurrentTimezoneModel();
            $timezoneData = $currentTimezoneModel->join('timezones', 'timezones.id = timezone_id')->find(1);
            $timezone = isset($timezoneData) ? $timezoneData['timezone'] : 'America/Los_Angeles';
            $modStart = $now->setTimezone($timezone);
            $scheduleId = $this->returnSchedule($modStart->getHour());
            $this->setVideo($video, $videoModel, $scheduleId);
            $this->addVideoToLog($video['id']);
            $this->getVideo($videoModel, true);
        }

        $promoData = [
            'id' => 1,
            'count' => $promoCount
        ];

        $promoModel->save($promoData);

        $nowPlaying = $playingModel->find(1);

        $now = new Time('now', 'UTC', 'en_US');
        $endTime = new Time($nowPlaying['end_time'], 'UTC', 'en_US');

        $difference = $now->difference($endTime);

        $seconds = $difference->getSeconds();

        if ($seconds < 0) $seconds = 0;

        $this->run($seconds);
    }

    private function getScheduledVideo($model, $scheduleId, $startString)
    {
        return $model->where('played', 0)->where('deleted', 0)->where('schedule_id', $scheduleId)->where('play_after <=', $startString)->where('id !=', 155)->orderBy('id', 'RANDOM')->first();
    }

    private function getUnscheduledVideo($model, $startString)
    {
        return $model->select('videos.id, video_id, video_length, start, end, played, regular_rotation, title, thumbnail, schedule_id, play_after')
            ->groupStart()
            ->where('schedule_id', 0)
            ->orWhere('regular_rotation', 1)
            ->groupEnd()
            ->where('played', 0)
            ->where('deleted', 0)
            ->where('play_after <=', $startString)
            ->where('videos.id !=', 155)
            ->join('schedule s', 'schedule_id = s.id', 'left')
            ->orderBy('id', 'RANDOM')->first();
    }

    private function getVideo($videoModel, $queued = false)
    {
        $now = new Time('now', 'UTC', 'en_US');
        $currentTimezoneModel = new CurrentTimezoneModel();
        $timezoneData = $currentTimezoneModel->join('timezones', 'timezones.id = timezone_id')->find(1);
        $timezone = isset($timezoneData) ? $timezoneData['timezone'] : 'America/Los_Angeles';

        if (!$queued) {
            $startString = $now->toDateTimeString();
            $modStart = $now->setTimezone($timezone);
        }
        else {
            $playingModel = new NowPlayingModel();
            $nowPlaying = $playingModel->find(1);
            $endTime = new Time($nowPlaying['end_time'], 'UTC', 'en_US');
            $difference = $now->difference($endTime);
            $start = $now->addSeconds($difference->getSeconds());
            $startString = $start->toDateTimeString();
            $modStart = $start->setTimezone($timezone);
        }

        $scheduleId = $this->returnSchedule($modStart->getHour());

        if ($scheduleId) {
            $video = $this->getScheduledVideo($videoModel, $scheduleId, $startString);

            if (!isset($video)) {
                $videoModel->set('played', 0)->where('schedule_id', $scheduleId)->update();
                $video = $this->getScheduledVideo($videoModel, $scheduleId, $startString);

                if (!isset($video)) {
                    $video = $videoModel->where('played', 0)->where('deleted', 0)->where('schedule_id', $scheduleId)->where('id !=', 155)->orderBy('id', 'RANDOM')->first();
                    if (!isset($video)) {
                        $scheduleId = 0;
                    }
                }
            }
        }

        if ($scheduleId === 0) {
            $video = $this->getUnscheduledVideo($videoModel, $startString);

            if (!isset($video)) {
                $videoModel->set('played', 0)->update();
                $video = $this->getUnscheduledVideo($videoModel, $startString);
            }
        }

        if ($this->isVideoValid($video, $videoModel)) {
            if (!$queued) {
                $this->setVideo($video, $videoModel, $scheduleId);
                $this->addVideoToLog($video['id']);
                $this->getVideo($videoModel, true);
            }
            else {
                $queuedModel = new QueuedVideoModel();
                $data = [
                    'id' => 1,
                    'videos_id' => $video['id']
                ];
                $queuedModel->save($data);
            }
        }
        else {
            $this->getVideo($videoModel, $queued);
        }
    }

    private function setVideo($video, $videoModel, $scheduleId = 0)
    {
        $playingModel = new NowPlayingModel();
        $start = new Time('now', 'UTC', 'en_US');
        $startString = $start->toDateTimeString();

        $videoLength = intval($video['video_length']);
        $startTime = intval($video['start']);
        $endTime = intval($video['end']);

        if ($startTime && $endTime) $videoLength = $endTime - $startTime;
        elseif ($startTime) $videoLength = $videoLength - $startTime;
        elseif ($endTime) $videoLength = $endTime;

        $end = $start->addSeconds($videoLength);

        $end = $end->toDateTimeString();

        $data = [
            'id' => 1,
            'videos_id' => $video['id'],
            'start_time' => $startString,
            'end_time' => $end
        ];

        $playingModel->insert($data);

        if (intval($video['id'] !== 155)) {
            $seconds = $this->addTimes($scheduleId, $videoModel);
            $playAfter = $start->addSeconds($seconds);
            $playAfter = $playAfter->toDateTimeString();

            $videoModel->update($video['id'], ['played' => 1, 'play_after' => $playAfter]);
        }
    }

    private function isVideoValid($video, $videoModel): bool
    {
        $body = $this->getVideoData($video['video_id']);
        $region = array();

        foreach ($body['items'] as $item) {
            $vTime = $item['contentDetails']['duration'];
            $title = $item['snippet']['title'];
            $thumbnail = $item['snippet']['thumbnails']['medium']['url'];
            $embed = $item['status']['embeddable'];
            if (isset($item['contentDetails']['regionRestriction']['blocked'])) $region = $item['contentDetails']['regionRestriction']['blocked'];
        }

        if (!isset($vTime) || !isset($title) || !isset($thumbnail) || !isset($embed)) {
            $videoModel->update($video['id'], ['deleted' => 1]);
            $this->emailAdmin('the video has been set to private or deleted', $video);
            return false;
        }
        if (!$embed) {
            $videoModel->update($video['id'], ['deleted' => 1]);
            $this->emailAdmin('playback on other websites has been disabled by the video owner', $video);
            return false;
        }

        if (count($region)) {
            $flip = array_flip($region);
            if (isset($flip['US'])) {
                $videoModel->update($video['id'], ['deleted' => 1]);
                $this->emailAdmin('video is unavailable in US', $video);
                return false;
            }
        }

        return true;
    }

    private function emailAdmin($msg, $video)
    {
        $email = \Config\Services::email();

        $domain = getenv('DOMAIN');

        $email->setFrom("noreply@{$domain}");
        $email->setTo('mench@slovenly.com');
        $email->setSubject("{$video['title']} has been removed from the database");
        $body = "<a href='https://www.youtube.com/watch?v={$video['video_id']}'>{$video['title']}</a> has been removed from the database because $msg.";
        $email->setMessage($body);
        $body = "'{$video['title']}' located at https://www.youtube.com/watch?v={$video['video_id']} has been removed from the database because $msg.";
        $email->setAltMessage($body);
        $email->send();
    }

    private function emailError($error)
    {
        $email = \Config\Services::email();

        $domain = getenv('DOMAIN');

        $email->setFrom("noreply@{$domain}");
        $email->setTo('stevenrobday@gmail.com');
        $email->setSubject("video playback error");
        $body = "$error";
        $email->setMessage($body);
        $body = "$error";
        $email->setAltMessage($body);
        $email->send();
    }

    private function returnSchedule($hour): int
    {
        $scheduleModel = new ScheduleModel();

        $rows = $scheduleModel->where('status', 1)->findAll();

        if (count($rows)) {
            $schedulesArray = array();
            $idArray = array();

            foreach ($rows as $row) {
                $schedulesArray[] = array_flip($this->timeArray($row['start_time'], $row['end_time']));
                $idArray[] = $row['id'];
            }

            $count = count($schedulesArray);

            for ($i = 0; $i < $count; $i++) {
                if (isset($schedulesArray[$i][$hour])) {
                    return intval($idArray[$i]);
                }
            }
            return 0;
        }
        else return 0;
    }

    private function addTimes($scheduleId, $model)
    {
        if ($scheduleId > 0) {
            $videos = $model->where('schedule_id', $scheduleId)->findAll();

            $seconds = 0;
            foreach ($videos as $v) {
                $length = $v['video_length'];
                $start = $v['start'];
                $end = $v['end'];
                if ($start || $end) {
                    if (!$end) $seconds += $length - $start;
                    elseif (!$start) $seconds += $end;
                    else $seconds += $end - $start;
                }
                else $seconds += $length;
            }

            return floor($seconds * 2/3);
        }

        return 3600;
    }

    private function addVideoToLog($videos_id)
    {
        $videoLogModel = new VideoLogModel();
        $datetime = new Time('now', 'UTC', 'en_US');

        $data = [
            'videos_id' => $videos_id,
            'datetime' => $datetime
        ];

        $videoLogModel->insert($data);
    }
}
<?php namespace App\Models;

use CodeIgniter\Model;

class VideoModel extends Model
{
    protected $table = 'videos';
    protected $allowedFields = ['video_id', 'video_length', 'start', 'end', 'played', 'title', 'thumbnail', 'schedule_id', 'play_after', 'deleted'];
    protected $primaryKey = 'id';
}
<?php namespace App\Models;

use CodeIgniter\Model;

class SuggestedVideoModel extends Model
{
    protected $table = 'suggested_videos';
    protected $allowedFields = ['video_id', 'video_length', 'title', 'thumbnail', 'start', 'end', 'regular_rotation', 'user_id', 'approved_by', 'status_id', 'schedule_id', 'comments'];
    protected $primaryKey = 'id';
}
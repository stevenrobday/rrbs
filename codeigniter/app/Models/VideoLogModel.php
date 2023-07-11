<?php namespace App\Models;

use CodeIgniter\Model;

class VideoLogModel extends Model
{
    protected $table = 'video_log';
    protected $allowedFields = ['videos_id', 'datetime'];
    protected $primaryKey = 'id';
}
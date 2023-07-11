<?php namespace App\Models;

use CodeIgniter\Model;

class QueuedVideoModel extends Model
{
    protected $table = 'queued_video';
    protected $allowedFields = ['id', 'videos_id'];
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
}
<?php namespace App\Models;

use CodeIgniter\Model;

class NowPlayingModel extends Model
{
    protected $table = 'now_playing';
    protected $allowedFields = ['id', 'videos_id', 'start_time', 'end_time'];
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
}
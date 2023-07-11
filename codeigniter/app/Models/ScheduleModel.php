<?php namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table = 'schedule';
    protected $allowedFields = ['category', 'start_time', 'end_time', 'status', 'regular_rotation'];
    protected $primaryKey = 'id';
}
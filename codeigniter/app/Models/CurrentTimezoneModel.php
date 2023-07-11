<?php namespace App\Models;

use CodeIgniter\Model;

class CurrentTimezoneModel extends Model
{
    protected $table = 'current_timezone';
    protected $allowedFields = ['id', 'timezone_id'];
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
}
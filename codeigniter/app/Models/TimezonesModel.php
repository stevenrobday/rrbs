<?php namespace App\Models;

use CodeIgniter\Model;

class TimezonesModel extends Model
{
    protected $table = 'timezones';
    protected $allowedFields = ['label', 'timezone'];
    protected $primaryKey = 'id';
}
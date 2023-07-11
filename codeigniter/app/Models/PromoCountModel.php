<?php namespace App\Models;

use CodeIgniter\Model;

class PromoCountModel extends Model
{
    protected $table = 'promo_count';
    protected $allowedFields = ['id','count'];
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
}
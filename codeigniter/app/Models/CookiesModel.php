<?php

namespace App\Models;
use CodeIgniter\Model;

class CookiesModel extends Model
{
    protected $table = 'cookies';
    protected $allowedFields = ['user_id', 'password_hash', 'selector_hash', 'expiration_date'];
    protected $primaryKey = 'id';
}

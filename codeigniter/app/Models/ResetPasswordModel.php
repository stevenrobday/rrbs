<?php namespace App\Models;

use CodeIgniter\Model;

class ResetPasswordModel extends Model
{
    protected $table = 'reset_password';
    protected $allowedFields = ['user_id', 'token_1', 'token_2', 'expiration_date'];
    protected $primaryKey = 'id';
}


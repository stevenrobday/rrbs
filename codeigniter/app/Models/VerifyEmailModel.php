<?php namespace App\Models;

use CodeIgniter\Model;

class VerifyEmailModel extends Model
{
    protected $table = 'verify_email';
    protected $allowedFields = ['user_id', 'token_1', 'token_2'];
    protected $primaryKey = 'id';
}


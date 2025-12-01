<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'users'; // Your admin table name
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'password'];
}

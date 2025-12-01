<?php

namespace App\Models;

use CodeIgniter\Model;

class UpdateModel extends Model
{
    protected $table = 'updates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['heading', 'type', 'documents', 'created_at', 'updated_at'];
}

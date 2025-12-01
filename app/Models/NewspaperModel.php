<?php

namespace App\Models;

use CodeIgniter\Model;

class NewspaperModel extends Model
{
    protected $table = 'newspapers'; // your table name
    protected $primaryKey = 'id';

    protected $allowedFields = ['documents', 'created_at', 'updated_at'];

    protected $useTimestamps = true; // automatically manage created_at & updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
}

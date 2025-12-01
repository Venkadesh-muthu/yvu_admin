<?php

namespace App\Models;

use CodeIgniter\Model;

class NewspaperModel extends Model
{
    protected $table = 'newspapers'; // your table name
    protected $primaryKey = 'id';

    // Add start_date and end_date here
    protected $allowedFields = [
        'documents',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true; // automatically manage created_at & updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
}

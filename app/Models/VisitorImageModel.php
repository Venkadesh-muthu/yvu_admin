<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitorImageModel extends Model
{
    protected $table = 'visitor_images';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'visitor_id',
        'image',
        'image_description',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class NewspaperImageModel extends Model
{
    protected $table = 'newspaper_images'; // table name
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'newspaper_id',
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

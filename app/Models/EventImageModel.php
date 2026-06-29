<?php

namespace App\Models;

use CodeIgniter\Model;

class EventImageModel extends Model
{
    protected $table = 'event_images'; // table name
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'event_id',
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

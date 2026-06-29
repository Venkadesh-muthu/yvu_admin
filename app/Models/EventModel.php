<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events'; // table name
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'description',
        'from_date',
        'to_date',
        'event_images',
        'event_document',
        'document_description',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class UpdateModel extends Model
{
    protected $table = 'updates';
    protected $primaryKey = 'id';

    // Add start_date and end_date also
    protected $allowedFields = [
        'heading',
        'type',
        'documents',
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];
}

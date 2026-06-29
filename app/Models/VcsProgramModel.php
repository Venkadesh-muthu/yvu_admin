<?php

namespace App\Models;

use CodeIgniter\Model;

class VcsProgramModel extends Model
{
    protected $table = 'vcs_programs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'description',
        'from_date',
        'to_date',
        'program_images',
        'program_document',
        'document_description',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class VcsProgramImageModel extends Model
{
    protected $table = 'vcs_program_images';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'vcs_program_id',
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

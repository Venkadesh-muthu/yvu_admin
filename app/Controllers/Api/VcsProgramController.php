<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\VcsProgramModel;
use App\Models\VcsProgramImageModel;

class VcsProgramController extends BaseController
{
    protected $programModel;
    protected $programImageModel;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $this->programModel = new VcsProgramModel();
        $this->programImageModel = new VcsProgramImageModel();
    }

    // ✅ Fetch all programs
    public function getPrograms()
    {
        $programs = $this->programModel
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($programs as $program) {

            $images = $this->programImageModel
                ->where('vcs_program_id', $program['id'])
                ->findAll();

            $imageData = [];

            foreach ($images as $img) {
                $imageData[] = [
                    'id' => $img['id'],
                    'image_url' => base_url('uploads/vcs/images/' . rawurlencode($img['image'])),
                    'description' => $img['image_description']
                ];
            }

            $data[] = [
                'id'          => $program['id'],
                'title'       => $program['title'],
                'description' => $program['description'],
                'from_date'   => $program['from_date'],
                'to_date'     => $program['to_date'],
                'program_images' => $imageData,
                'program_document' => !empty($program['program_document'])
                    ? base_url('uploads/vcs/documents/' . rawurlencode($program['program_document']))
                    : null,
                'document_description' => $program['document_description'] ?? null,
                'created_at'  => $program['created_at'],
                'updated_at'  => $program['updated_at'] ?? null,
            ];
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    // ✅ Fetch single program
    public function getProgram($id)
    {
        $program = $this->programModel->find($id);

        if (!$program) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Program not found.'
            ]);
        }

        $images = $this->programImageModel
            ->where('vcs_program_id', $id)
            ->findAll();

        $imageData = [];

        foreach ($images as $img) {
            $imageData[] = [
                'id' => $img['id'],
                'image_url' => base_url('uploads/vcs/images/' . rawurlencode($img['image'])),
                'description' => $img['image_description']
            ];
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'data'   => [
                'id'          => $program['id'],
                'title'       => $program['title'],
                'description' => $program['description'],
                'from_date'   => $program['from_date'],
                'to_date'     => $program['to_date'],
                'program_images' => $imageData,
                'program_document' => !empty($program['program_document'])
                    ? base_url('uploads/vcs/documents/' . rawurlencode($program['program_document']))
                    : null,
                'document_description' => $program['document_description'] ?? null,
                'created_at'  => $program['created_at'],
                'updated_at'  => $program['updated_at'] ?? null,
            ]
        ]);
    }
}

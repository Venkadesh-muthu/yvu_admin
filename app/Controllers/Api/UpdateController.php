<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UpdateModel;

class UpdateController extends BaseController
{
    protected $updateModel;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        $this->updateModel = new UpdateModel();
    }

    // ✅ Fetch all updates
    public function getUpdates()
    {
        $today = date('Y-m-d');

        // Fetch updates with start/end date filter including NULL values
        $updates = $this->updateModel
            ->where("(start_date IS NULL OR start_date <= '$today')")
            ->where("(end_date IS NULL OR end_date >= '$today')")
            ->orderBy('id', 'DESC')
            ->findAll();

        // Format document URLs
        $updates = array_map(function ($item) {
            $files = json_decode($item['documents'] ?? '[]', true);
            $fileUrls = [];

            foreach ($files as $file) {
                $fileUrls[] = base_url('uploads/updates/' . rawurlencode($file));
            }

            return [
                'id'         => $item['id'],
                'heading'    => $item['heading'],
                'type'       => $item['type'] ?? null,
                'documents'  => $fileUrls,
                'start_date' => $item['start_date'] ?? null,
                'end_date'   => $item['end_date'] ?? null,
                'created_at' => $item['created_at'] ?? null,
                'updated_at' => $item['updated_at'] ?? null,
            ];
        }, $updates);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $updates
        ]);
    }


    // ✅ Fetch a single update by ID
    public function getUpdate($id)
    {
        $update = $this->updateModel->find($id);

        if (!$update) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Update not found.'
            ]);
        }

        $files = json_decode($update['documents'] ?? '[]', true);
        $fileUrls = [];

        foreach ($files as $file) {
            $fileUrls[] = base_url('uploads/updates/' . rawurlencode($file));
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'id'         => $update['id'],
                'heading'    => $update['heading'],
                'type'       => $update['type'] ?? null, // ✅ include type field here also
                'documents'  => $fileUrls,
                'created_at' => $update['created_at'] ?? null,
                'updated_at' => $update['updated_at'] ?? null,
            ]
        ]);
    }
}

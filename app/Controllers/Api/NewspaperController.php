<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\NewspaperModel;

class NewspaperController extends BaseController
{
    protected $newspaperModel;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $this->newspaperModel = new NewspaperModel();
    }

    // ✅ Fetch all newspapers
    public function getNewspapers()
    {
        $today = date('Y-m-d');

        // Fetch with date filter including NULL dates
        $newspapers = $this->newspaperModel
            ->where("(start_date IS NULL OR start_date <= '$today')")
            ->where("(end_date IS NULL OR end_date >= '$today')")
            ->orderBy('id', 'DESC')
            ->findAll();

        // Format document URLs for API
        $newspapers = array_map(function ($item) {
            $files = json_decode($item['documents'] ?? '[]', true);
            $fileUrls = [];

            foreach ($files as $file) {
                $fileUrls[] = base_url('uploads/newspapers/' . rawurlencode($file));
            }

            return [
                'id'         => $item['id'],
                'documents'  => $fileUrls,
                'start_date' => $item['start_date'] ?? null,
                'end_date'   => $item['end_date'] ?? null,
                'created_at' => $item['created_at'] ?? null,
                'updated_at' => $item['updated_at'] ?? null,
            ];
        }, $newspapers);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $newspapers
        ]);
    }


    // ✅ Fetch a single newspaper by ID
    public function getNewspaper($id)
    {
        $newspaper = $this->newspaperModel->find($id);

        if (!$newspaper) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Newspaper not found.'
            ]);
        }

        $files = json_decode($newspaper['documents'] ?? '[]', true);
        $fileUrls = [];

        foreach ($files as $file) {
            $fileUrls[] = base_url('uploads/newspapers/' . rawurlencode($file));
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'id'        => $newspaper['id'],
                'documents' => $fileUrls,
                'created_at' => $newspaper['created_at'] ?? null,
                'updated_at' => $newspaper['updated_at'] ?? null,
            ]
        ]);
    }
}

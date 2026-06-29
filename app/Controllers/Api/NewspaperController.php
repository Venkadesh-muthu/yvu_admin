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

    // ✅ Fetch all newspapers (Expire after 7 days)
    public function getNewspapers()
    {
        $today = date('Y-m-d');

        $newspapers = $this->newspaperModel
            ->groupBy('id')
            ->orderBy('id', 'DESC')
            ->findAll();

        $newspapers = array_filter($newspapers, function ($item) use ($today) {
            $createdAt = $item['created_at'] ?? null;
            if (!$createdAt) {
                return false;
            }

            $expireDate = date('Y-m-d', strtotime($createdAt . ' +7 days'));
            return $expireDate >= $today;
        });

        $newspapers = array_values(array_map(function ($item) {

            $files = json_decode($item['documents'] ?? '[]', true);
            $fileUrls = [];

            foreach ($files as $file) {
                $fileUrls[] = base_url('uploads/newspapers/' . rawurlencode($file));
            }

            $createdAt = $item['created_at'] ?? null;
            $expiresAt = $createdAt
                ? date('Y-m-d', strtotime($createdAt . ' +7 days'))
                : null;

            return [
                'id'         => $item['id'],
                'title'      => $item['title'], // ✅ IMPORTANT
                'publish_date' => $item['publish_date'],
                'documents'  => $fileUrls,
                'created_at' => $createdAt,
                'updated_at' => $item['updated_at'] ?? null,
                'expires_at' => $expiresAt,
            ];
        }, $newspapers));

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $newspapers
        ]);
    }

    public function allNewspapers()
    {
        // Fetch all newspapers
        $newspapers = $this->newspaperModel
            ->groupBy('id')
            ->orderBy('id', 'DESC')
            ->findAll();

        // Format response
        $newspapers = array_values(array_map(function ($item) {

            // Decode documents
            $files = json_decode($item['documents'] ?? '[]', true);
            $fileUrls = [];

            foreach ($files as $file) {
                $fileUrls[] = base_url('uploads/newspapers/' . rawurlencode($file));
            }

            return [
                'id'           => $item['id'],
                'title'        => $item['title'],
                'publish_date' => $item['publish_date'],
                'documents'    => $fileUrls,
                'created_at'   => $item['created_at'],
                'updated_at'   => $item['updated_at'] ?? null,
            ];
        }, $newspapers));

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

        $createdAt = $newspaper['created_at'] ?? null;
        $expiresAt = $createdAt
            ? date('Y-m-d', strtotime($createdAt . ' +7 days'))
            : null;

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => [
                'id'         => $newspaper['id'],
                'documents'  => $fileUrls,
                'created_at' => $createdAt,
                'updated_at' => $newspaper['updated_at'] ?? null,
                'expires_at' => $expiresAt, // ✅ new field
            ]
        ]);
    }
}

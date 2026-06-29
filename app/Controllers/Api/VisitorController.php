<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\VisitorModel;
use App\Models\VisitorImageModel;

class VisitorController extends BaseController
{
    protected $visitorModel;
    protected $visitorImageModel;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $this->visitorModel = new VisitorModel();
        $this->visitorImageModel = new VisitorImageModel();
    }

    // ✅ Fetch all visitors
    public function getVisitors()
    {
        $visitors = $this->visitorModel
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($visitors as $visitor) {

            $images = $this->visitorImageModel
                ->where('visitor_id', $visitor['id'])
                ->findAll();

            $imageData = [];

            foreach ($images as $img) {
                $imageData[] = [
                    'id' => $img['id'],
                    'image_url' => base_url('uploads/visitors/images/' . rawurlencode($img['image'])),
                    'description' => $img['image_description']
                ];
            }

            $data[] = [
                'id'          => $visitor['id'],
                'title'       => $visitor['title'],
                'description' => $visitor['description'],
                'from_date'   => $visitor['from_date'],
                'to_date'     => $visitor['to_date'],
                'visitor_images' => $imageData,
                'visitor_document' => !empty($visitor['visitor_document'])
                    ? base_url('uploads/visitors/documents/' . rawurlencode($visitor['visitor_document']))
                    : null,
                'document_description' => $visitor['document_description'] ?? null,
                'created_at'  => $visitor['created_at'],
                'updated_at'  => $visitor['updated_at'] ?? null,
            ];
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    // ✅ Fetch single visitor
    public function getVisitor($id)
    {
        $visitor = $this->visitorModel->find($id);

        if (!$visitor) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Visitor not found.'
            ]);
        }

        $images = $this->visitorImageModel
            ->where('visitor_id', $id)
            ->findAll();

        $imageData = [];

        foreach ($images as $img) {
            $imageData[] = [
                'id' => $img['id'],
                'image_url' => base_url('uploads/visitors/images/' . rawurlencode($img['image'])),
                'description' => $img['image_description']
            ];
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'data'   => [
                'id'          => $visitor['id'],
                'title'       => $visitor['title'],
                'description' => $visitor['description'],
                'from_date'   => $visitor['from_date'],
                'to_date'     => $visitor['to_date'],
                'visitor_images' => $imageData,
                'visitor_document' => !empty($visitor['visitor_document'])
                    ? base_url('uploads/visitors/documents/' . rawurlencode($visitor['visitor_document']))
                    : null,
                'document_description' => $visitor['document_description'] ?? null,
                'created_at'  => $visitor['created_at'],
                'updated_at'  => $visitor['updated_at'] ?? null,
            ]
        ]);
    }
}

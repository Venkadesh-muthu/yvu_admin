<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\GalleryModel;
use App\Models\GalleryImageModel;

class GalleryController extends BaseController
{
    protected $galleryModel;
    protected $galleryImageModel;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $this->galleryModel = new GalleryModel();
        $this->galleryImageModel = new GalleryImageModel();
    }

    // ✅ Fetch all galleries
    public function getGalleries()
    {
        $galleries = $this->galleryModel
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($galleries as $gallery) {

            $images = $this->galleryImageModel
                ->where('gallery_id', $gallery['id'])
                ->findAll();

            $imageData = [];

            foreach ($images as $img) {
                $imageData[] = [
                    'id' => $img['id'],
                    'image_url' => base_url('uploads/gallery/images/' . rawurlencode($img['image'])),
                    'description' => $img['image_description']
                ];
            }

            $data[] = [
                'id'          => $gallery['id'],
                'title'       => $gallery['title'],
                'description' => $gallery['description'],
                'from_date'   => $gallery['from_date'],
                'to_date'     => $gallery['to_date'],
                'gallery_images' => $imageData,
                'gallery_document' => !empty($gallery['gallery_document'])
                    ? base_url('uploads/gallery/documents/' . rawurlencode($gallery['gallery_document']))
                    : null,
                'document_description' => $gallery['document_description'] ?? null,
                'created_at'  => $gallery['created_at'],
                'updated_at'  => $gallery['updated_at'] ?? null,
            ];
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    // ✅ Fetch single gallery
    public function getGallery($id)
    {
        $gallery = $this->galleryModel->find($id);

        if (!$gallery) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Gallery not found.'
            ]);
        }

        $images = $this->galleryImageModel
            ->where('gallery_id', $id)
            ->findAll();

        $imageData = [];

        foreach ($images as $img) {
            $imageData[] = [
                'id' => $img['id'],
                'image_url' => base_url('uploads/gallery/images/' . rawurlencode($img['image'])),
                'description' => $img['image_description']
            ];
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'data'   => [
                'id'          => $gallery['id'],
                'title'       => $gallery['title'],
                'description' => $gallery['description'],
                'from_date'   => $gallery['from_date'],
                'to_date'     => $gallery['to_date'],
                'gallery_images' => $imageData,
                'gallery_document' => !empty($gallery['gallery_document'])
                    ? base_url('uploads/gallery/documents/' . rawurlencode($gallery['gallery_document']))
                    : null,
                'document_description' => $gallery['document_description'] ?? null,
                'created_at'  => $gallery['created_at'],
                'updated_at'  => $gallery['updated_at'] ?? null,
            ]
        ]);
    }
}

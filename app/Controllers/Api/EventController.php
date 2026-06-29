<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\EventImageModel;

class EventController extends BaseController
{
    protected $eventModel;
    protected $eventImageModel;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        $this->eventModel = new EventModel();
        $this->eventImageModel = new EventImageModel();
    }

    // ✅ Fetch all events
    public function getEvents()
    {
        $events = $this->eventModel
            ->orderBy('id', 'DESC')
            ->findAll();

        $data = [];

        foreach ($events as $event) {

            $images = $this->eventImageModel
                           ->where('event_id', $event['id'])
                           ->findAll();

            $imageData = [];

            foreach ($images as $img) {
                $imageData[] = [
                    'id' => $img['id'],
                    'image_url' => base_url('uploads/events/images/' . rawurlencode($img['image'])),
                    'description' => $img['image_description']
                ];
            }

            $data[] = [
                'id'          => $event['id'],
                'title'       => $event['title'],
                'description' => $event['description'],
                'from_date'   => $event['from_date'],
                'to_date'     => $event['to_date'],
                'event_images' => $imageData,
                'event_document' => !empty($event['event_document'])
                    ? base_url('uploads/events/documents/' . rawurlencode($event['event_document']))
                    : null,
                'document_description' => $event['document_description'] ?? null,
                'created_at'  => $event['created_at'],
                'updated_at'  => $event['updated_at'] ?? null,
            ];
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    // ✅ Fetch single event
    public function getEvent($id)
    {
        $event = $this->eventModel->find($id);

        if (!$event) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Event not found.'
            ]);
        }

        $images = $this->eventImageModel
                       ->where('event_id', $id)
                       ->findAll();

        $imageData = [];

        foreach ($images as $img) {
            $imageData[] = [
                'id' => $img['id'],
                'image_url' => base_url('uploads/events/images/' . rawurlencode($img['image'])),
                'description' => $img['image_description']
            ];
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status' => 'success',
            'data'   => [
                'id'          => $event['id'],
                'title'       => $event['title'],
                'description' => $event['description'],
                'from_date'   => $event['from_date'],
                'to_date'     => $event['to_date'],
                'event_images' => $imageData,
                'event_document' => !empty($event['event_document'])
                    ? base_url('uploads/events/documents/' . rawurlencode($event['event_document']))
                    : null,
                'document_description' => $event['document_description'] ?? null,
                'created_at'  => $event['created_at'],
                'updated_at'  => $event['updated_at'] ?? null,
            ]
        ]);
    }
}

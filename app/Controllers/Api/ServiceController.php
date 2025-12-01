<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class ServiceController extends BaseController
{
    public function sendRequest()
    {
        $service    = $this->request->getPost('service');
        $date       = $this->request->getPost('date');
        $time_from  = $this->request->getPost('time_from');
        $time_to    = $this->request->getPost('time_to');
        $venue      = $this->request->getPost('venue');

        $name       = $this->request->getPost('name');
        $email      = $this->request->getPost('email');
        $mobile     = $this->request->getPost('mobile');
        $department = $this->request->getPost('department');

        if (!$service || !$date || !$time_from || !$time_to || !$venue ||
            !$name || !$email || !$mobile || !$department) {
            return $this->response->setStatusCode(400)
                ->setJSON(['status' => false, 'message' => 'Missing fields']);
        }

        $emailService = \Config\Services::email();

        $to = "services@yvu.edu.in";
        $subject = "SERVICE REQUEST - " . $service;

        $message = "
            <h2>SERVICE REQUEST</h2>

            <p><strong>Service:</strong> $service</p>
            <p><strong>Date:</strong> $date</p>
            <p><strong>Time From:</strong> $time_from</p>
            <p><strong>Time To:</strong> $time_to</p>
            <p><strong>Venue:</strong> $venue</p>

            <hr>

            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Mobile:</strong> $mobile</p>
            <p><strong>Department:</strong> $department</p>
        ";

        $emailService->setTo($to);
        $emailService->setFrom($email);
        $emailService->setReplyTo($email, $name);
        $emailService->setSubject($subject);
        $emailService->setMessage($message);

        if ($emailService->send()) {
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Request sent successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Email sending failed!',
                'error' => $emailService->printDebugger(['headers'])
            ]);
        }
    }
}

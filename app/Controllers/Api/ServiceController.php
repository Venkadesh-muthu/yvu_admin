<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class ServiceController extends BaseController
{
    public function sendRequest()
    {
        // Get POST data
        $service    = $this->request->getPost('service');
        $name       = $this->request->getPost('name');
        $email      = $this->request->getPost('email');
        $mobile     = $this->request->getPost('mobile');
        $department = $this->request->getPost('department');

        // Validation
        if (!$service || !$name || !$email || !$mobile || !$department) {
            return $this->response->setStatusCode(400)
                                  ->setJSON(['status' => false, 'message' => 'Missing fields']);
        }

        // Email settings
        $emailService = \Config\Services::email();

        $to = "services@yvu.edu.in";
        $subject = "SERVICE REQUEST - " . $service;

        $message = "
            <h2>SERVICE REQUEST</h2>

            <p><strong>Service:</strong> $service</p>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Mobile:</strong> $mobile</p>
            <p><strong>Department:</strong> $department</p>
        ";

        // Set email data
        $emailService->setTo($to);
        $emailService->setFrom($email); // better for SMTP
        $emailService->setReplyTo($email, $name); // user's email
        $emailService->setSubject($subject);
        $emailService->setMessage($message);

        // Try sending email
        if ($emailService->send()) {
            return $this->response->setStatusCode(200)
                                  ->setJSON(['status' => true, 'message' => 'Request sent successfully!']);
        } else {
            return $this->response->setStatusCode(500)
                                  ->setJSON([
                                      'status' => false,
                                      'message' => 'Email sending failed!',
                                      'error' => $emailService->printDebugger(['headers'])
                                  ]);
        }
    }
}

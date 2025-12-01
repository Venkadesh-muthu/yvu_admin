<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once APPPATH . 'Libraries/PHPMailer/src/PHPMailer.php';
require_once APPPATH . 'Libraries/PHPMailer/src/SMTP.php';
require_once APPPATH . 'Libraries/PHPMailer/src/Exception.php';

class ServiceController extends BaseController
{
    public function sendRequest()
    {
        // CORS headers
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        // Handle OPTIONS preflight
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200)->setJSON(['status' => 'ok']);
        }

        // Get POST data
        $service    = $this->request->getPost('service');
        $name       = $this->request->getPost('name');
        $email      = $this->request->getPost('email');
        $mobile     = $this->request->getPost('mobile');
        $department = $this->request->getPost('department');

        if (!$service || !$name || !$email || !$mobile || !$department) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please fill all fields'
            ]);
        }

        $mail = new PHPMailer(true);

        try {
            // Use PHP mail function
            $mail->isMail();

            $mail->setFrom($email, $name);
            $mail->addAddress('venkatesh@srivatech.com'); // recipient
            $mail->addReplyTo($email, $name);

            $mail->isHTML(false);
            $mail->Subject = "SERVICE REQUEST - $service";
            $mail->Body    = "Service: $service\nName: $name\nEmail: $email\nMobile: $mobile\nDepartment: $department";

            $mail->send();

            // Always send clean JSON
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Service Request Sent Successfully!'
            ]);
        } catch (Exception $e) {
            // Capture PHPMailer errors
            return $this->response->setJSON([
                'status' => 'failed',
                'message' => 'Mail could not be sent: ' . $mail->ErrorInfo
            ]);
        }
    }
}

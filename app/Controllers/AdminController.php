<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\UpdateModel;
use App\Models\NewspaperModel;

require_once APPPATH . 'Libraries/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController extends BaseController
{
    protected $adminModel;
    protected $updateModel;
    protected $newspaperModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->adminModel = new AdminModel();
        $this->updateModel = new UpdateModel();
        $this->newspaperModel = new NewspaperModel();

    }

    // --------------------------------
    // 🔐 AUTHENTICATION
    // --------------------------------
    public function index()
    {
        return view('admin/login');
    }

    public function login()
    {
        helper(['form']);
        $session = session();

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->adminModel
            ->where('email', $email)
            ->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid email or password');
        }

        // ✅ Store session with user_type
        $session->set([
            'isAdminLoggedIn' => true,
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'email'      => $user['email'],
            'phone'      => $user['phone'],
            'profile_pic' => $user['profile_pic'],

            // IMPORTANT
            'user_type'  => $user['user_type'], // super_admin | admin | newsadmin | user
            'login_time' => date('Y-m-d H:i:s')
        ]);

        // ✅ Redirect based on user_type
        if ($user['user_type'] === 'super_admin') {
            return redirect()->to('/dashboard');
        } elseif ($user['user_type'] === 'admin') {
            return redirect()->to('/dashboard');
        } elseif ($user['user_type'] === 'news_admin') {
            return redirect()->to('/dashboard');
        }

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
    public function downloadUpdatesPdf()
    {
        // 🔐 Allow only admin & super_admin
        if (!in_array(session()->get('user_type'), ['admin', 'super_admin'])) {
            return redirect()->to('updates')->with('error', 'Unauthorized');
        }

        // 📥 Get all updates
        $updates = $this->updateModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // 🧱 Build HTML manually
        $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
            h3 { text-align: center; margin-bottom: 15px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #000; padding: 6px; }
            th { background-color: #f2f2f2; }
        </style>
    </head>
    <body>
        <h3>Updates Report</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>';

        $i = 1;
        foreach ($updates as $u) {
            $html .= '
            <tr>
                <td>' . $i++ . '</td>
                <td>' . esc($u['heading']) . '</td>
                <td>' . esc($u['type'] ?? '-') . '</td>
                <td>' . (!empty($u['start_date']) ? date('d M Y', strtotime($u['start_date'])) : '-') . '</td>
                <td>' . (!empty($u['end_date']) ? date('d M Y', strtotime($u['end_date'])) : '-') . '</td>
                <td>' . date('d M Y', strtotime($u['created_at'])) . '</td>
            </tr>';
        }

        $html .= '
            </tbody>
        </table>
    </body>
    </html>';

        // ⚙ Dompdf setup
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // 📤 Download PDF
        return $dompdf->stream(
            'updates_' . date('Ymd_His') . '.pdf',
            ['Attachment' => true]
        );
    }
    // --------------------------------
    // 🧭 DASHBOARD
    // --------------------------------
    public function dashboard()
    {

        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $totalUpdates = $this->updateModel->countAllResults();

        $data = [
            'title' => 'Dashboard',
            'total_updates' => $totalUpdates,
            'content' => 'admin/dashboard',
        ];

        return view('admin/layout/templates', $data);
    }

    // --------------------------------
    // 🔔 NOTIFICATIONS CRUD
    // --------------------------------
    public function updates()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $perPage = 10; // you can change this

        $updates = $this->updateModel
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default');

        $pager = $this->updateModel->pager;

        $data = [
            'title'   => 'Updates',
            'updates' => $updates,
            'pager'   => $pager,
            'content' => 'admin/updates'
        ];

        return view('admin/layout/templates', $data);
    }


    public function addUpdate()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {

            $rules = [
                'heading'     => 'required',
                'type'        => 'required',
                'start_date'  => 'required|valid_date[Y-m-d]',
                'end_date'    => 'required|valid_date[Y-m-d]',
                'documents' => [
                    'rules' => 'uploaded[documents]|max_size[documents,10240]|ext_in[documents,pdf,doc,docx,xls,xlsx]',
                    'errors' => [
                        'uploaded' => 'Please upload at least one document.',
                        'max_size' => 'Each file must be less than 10MB.',
                        'ext_in'   => 'Only PDF, DOC, DOCX, XLS, XLSX files are allowed.'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                return view('admin/layout/templates', [
                    'title' => 'Add Update',
                    'content' => 'admin/add_update',
                    'validation' => $this->validator
                ]);
            }

            $uploadedFiles = $this->request->getFiles();
            $fileNames = [];

            if (isset($uploadedFiles['documents'])) {
                foreach ($uploadedFiles['documents'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getClientName();
                        $file->move(FCPATH . 'uploads/updates/', $newName);
                        $fileNames[] = $newName;
                    }
                }
            }

            $this->updateModel->set([
                'heading'     => $this->request->getPost('heading'),
                'type'        => $this->request->getPost('type'),
                'start_date'  => $this->request->getPost('start_date'),
                'end_date'    => $this->request->getPost('end_date'),
                'documents'   => json_encode($fileNames)
            ])
            ->set('created_at', 'CONVERT_TZ(NOW(), "SYSTEM", "+05:30")', false)
            ->insert();

            return redirect()->to('/updates')->with('success', 'Update added successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Add Update',
            'content' => 'admin/add_update'
        ]);
    }



    public function editUpdate($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $update = $this->updateModel->find($id);

        if (!$update) {
            return redirect()->to('/updates')->with('error', 'Update not found.');
        }

        if ($this->request->getMethod() === 'POST') {

            $rules = [
                'heading'     => 'required',
                'type'        => 'required',
                'start_date'  => 'required|valid_date[Y-m-d]',
                'end_date'    => 'required|valid_date[Y-m-d]',
            ];

            if (!$this->validate($rules)) {
                return view('admin/layout/templates', [
                    'title'   => 'Edit Update',
                    'update'  => $update,
                    'content' => 'admin/edit_update',
                    'validation' => $this->validator
                ]);
            }

            $uploadedFiles = $this->request->getFiles();
            $existingDocs = json_decode($update['documents'], true) ?? [];
            $newFiles = [];

            if (isset($uploadedFiles['documents'])) {
                foreach ($uploadedFiles['documents'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getClientName();
                        $file->move(FCPATH . 'uploads/updates/', $newName);
                        $newFiles[] = $newName;
                    }
                }
            }

            $mergedDocs = array_merge($existingDocs, $newFiles);

            $this->updateModel->set([
                'heading'     => $this->request->getPost('heading'),
                'type'        => $this->request->getPost('type'),
                'start_date'  => $this->request->getPost('start_date'),
                'end_date'    => $this->request->getPost('end_date'),
                'documents'   => json_encode($mergedDocs)
            ])
            ->set('updated_at', 'CONVERT_TZ(NOW(), "SYSTEM", "+05:30")', false)
            ->where('id', $id)
            ->update();

            return redirect()->to('/updates')->with('success', 'Update modified successfully.');
        }

        return view('admin/layout/templates', [
            'title'   => 'Edit Update',
            'update'  => $update,
            'content' => 'admin/edit_update'
        ]);
    }



    public function deleteFile($id, $index)
    {
        $update = $this->updateModel->find($id);
        if (!$update) {
            return redirect()->back()->with('error', 'Update not found.');
        }

        $files = json_decode($update['documents'], true) ?? [];

        if (isset($files[$index])) {
            $filePath = FCPATH . 'uploads/updates/' . $files[$index];

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            unset($files[$index]);
            $files = array_values($files);

            $this->updateModel->update($id, [
                'documents' => json_encode($files)
            ]);

            return redirect()->back()->with('success', 'File deleted successfully.');
        }

        return redirect()->back()->with('error', 'File not found.');
    }


    public function deleteUpdate($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $update = $this->updateModel->find($id);

        if (!$update) {
            return redirect()->to('/updates')->with('error', 'Update not found.');
        }

        $files = json_decode($update['documents'], true);
        if ($files && is_array($files)) {
            foreach ($files as $file) {
                $path = FCPATH . 'uploads/updates/' . $file;
                if (is_file($path)) {
                    unlink($path);
                }
            }
        }

        $this->updateModel->delete($id);

        return redirect()->to('/updates')->with('success', 'Update deleted successfully.');
    }

    public function newspapers()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $perPage = 10;

        $newspapers = $this->newspaperModel
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default');

        $pager = $this->newspaperModel->pager;

        $data = [
            'title'      => 'Newspapers',
            'newspapers' => $newspapers,
            'pager'      => $pager,
            'content'    => 'admin/newspapers'
        ];

        return view('admin/layout/templates', $data);
    }



    public function addNewspaper()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {

            // Validation rules
            $rules = [
                'documents' => [
                    'rules' => 'uploaded[documents]|max_size[documents,10240]',
                    'errors' => [
                        'uploaded' => 'Please upload at least one document.',
                        'max_size' => 'Each file must be less than 10MB.'
                    ]
                ],
                'start_date' => 'required|valid_date[Y-m-d]',
                'end_date'   => 'required|valid_date[Y-m-d]'
            ];

            // Custom error messages
            $errors = [
                'end_date' => [
                    'rules' => 'required|valid_date[Y-m-d]|end_date_check',
                    'errors' => [
                        'end_date_check' => 'End Date must be equal or after Start Date.'
                    ]
                ]

            ];

            if (!$this->validate($rules, $errors)) {
                return view('admin/layout/templates', [
                    'title' => 'Add Newspaper',
                    'content' => 'admin/add_newspaper',
                    'validation' => $this->validator
                ]);
            }

            // Handle file uploads
            $uploadedFiles = $this->request->getFiles();
            $fileNames = [];

            if (isset($uploadedFiles['documents'])) {
                foreach ($uploadedFiles['documents'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getClientName();
                        $file->move(FCPATH . 'uploads/newspapers/', $newName);
                        $fileNames[] = $newName;
                    }
                }
            }

            // Save to database
            $this->newspaperModel
                ->set('documents', json_encode($fileNames))
                ->set('start_date', $this->request->getPost('start_date'))
                ->set('end_date', $this->request->getPost('end_date'))
                ->set('created_at', 'CONVERT_TZ(NOW(), "SYSTEM", "+05:30")', false)
                ->insert();

            return redirect()->to('/newspapers')->with('success', 'Newspaper uploaded successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Add Newspaper',
            'content' => 'admin/add_newspaper'
        ]);
    }

    public function editNewspaper($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $newspaper = $this->newspaperModel->find($id);
        if (!$newspaper) {
            return redirect()->to('/newspapers')->with('error', 'Newspaper not found.');
        }

        if ($this->request->getMethod() === 'POST') {

            // Validation rules
            $rules = [
                'documents' => [
                    'rules' => 'max_size[documents,10240]',
                    'errors' => [
                        'max_size' => 'Each file must be less than 10MB.'
                    ]
                ],
                'start_date' => 'required|valid_date[Y-m-d]',
                'end_date'   => 'required|valid_date[Y-m-d]'
            ];

            $errors = [
               'end_date' => [
                    'rules' => 'required|valid_date[Y-m-d]|end_date_check',
                    'errors' => [
                        'end_date_check' => 'End Date must be equal or after Start Date.'
                    ]
                ]

            ];

            if (!$this->validate($rules, $errors)) {
                return view('admin/layout/templates', [
                    'title' => 'Edit Newspaper',
                    'content' => 'admin/edit_newspaper',
                    'newspaper' => $newspaper,
                    'validation' => $this->validator
                ]);
            }

            // Handle file uploads
            $uploadedFiles = $this->request->getFiles();
            $existingDocs = json_decode($newspaper['documents'], true) ?? [];
            $newFiles = [];

            if (isset($uploadedFiles['documents'])) {
                foreach ($uploadedFiles['documents'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getClientName();
                        $file->move(FCPATH . 'uploads/newspapers/', $newName);
                        $newFiles[] = $newName;
                    }
                }
            }

            $mergedDocs = array_merge($existingDocs, $newFiles);

            // Update database
            $this->newspaperModel
                ->set('documents', json_encode($mergedDocs))
                ->set('start_date', $this->request->getPost('start_date'))
                ->set('end_date', $this->request->getPost('end_date'))
                ->set('updated_at', 'CONVERT_TZ(NOW(), "SYSTEM", "+05:30")', false)
                ->where('id', $id)
                ->update();

            return redirect()->to('/newspapers')->with('success', 'Newspaper updated successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Edit Newspaper',
            'newspaper' => $newspaper,
            'content' => 'admin/edit_newspaper'
        ]);
    }

    public function deleteNewspaperFile($id, $index)
    {
        $newspaper = $this->newspaperModel->find($id);
        if (!$newspaper) {
            return redirect()->back()->with('error', 'Newspaper not found.');
        }

        $files = json_decode($newspaper['documents'], true) ?? [];

        if (isset($files[$index])) {
            $filePath = FCPATH . 'uploads/newspapers/' . $files[$index];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            unset($files[$index]);
            $files = array_values($files);

            $this->newspaperModel->update($id, [
                'documents' => json_encode($files)
            ]);

            return redirect()->back()->with('success', 'File deleted successfully.');
        }

        return redirect()->back()->with('error', 'File not found.');
    }
    public function deleteNewspaper($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $newspaper = $this->newspaperModel->find($id);
        if (!$newspaper) {
            return redirect()->to('/newspapers')->with('error', 'Newspaper not found.');
        }

        $files = json_decode($newspaper['documents'], true) ?? [];
        foreach ($files as $file) {
            $path = FCPATH . 'uploads/newspapers/' . $file;
            if (is_file($path)) {
                unlink($path);
            }
        }

        $this->newspaperModel->delete($id);

        return redirect()->to('/newspapers')->with('success', 'Newspaper deleted successfully.');
    }
    // --------------------------------
    // 🛡️ LOGIN CHECK
    // --------------------------------
    private function checkLogin()
    {
        if (!session()->get('isAdminLoggedIn')) {
            return redirect()->to('/')->send();
        }
    }
}

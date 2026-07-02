<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use App\Models\UpdateModel;
use App\Models\NewspaperModel;
use App\Models\EventModel;
use App\Models\EventImageModel;
use App\Models\VisitorModel;
use App\Models\VisitorImageModel;
use App\Models\GalleryModel;
use App\Models\GalleryImageModel;
use App\Models\VcsProgramModel;
use App\Models\VcsProgramImageModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController extends BaseController
{
    protected $adminModel;
    protected $updateModel;
    protected $newspaperModel;
    protected $eventModel;
    protected $eventImageModel;
    protected $visitorModel;
    protected $visitorImageModel;
    protected $galleryModel;
    protected $galleryImageModel;
    protected $vcsProgramModel;
    protected $vcsProgramImageModel;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_LOCK_SECONDS = 900;

    public function __construct()
    {
        helper(['form', 'url', 'captcha']);
        $this->adminModel = new AdminModel();
        $this->visitorModel = new VisitorModel();
        $this->visitorImageModel = new VisitorImageModel();
        $this->galleryModel = new GalleryModel();
        $this->galleryImageModel = new GalleryImageModel();
        $this->vcsProgramModel = new VcsProgramModel();
        $this->vcsProgramImageModel = new VcsProgramImageModel();
        $this->updateModel = new UpdateModel();
        $this->newspaperModel = new NewspaperModel();
        $this->eventModel    = new EventModel();
        $this->eventImageModel = new EventImageModel();

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
        $email = (string) $this->request->getPost('email');

        if ($this->isLoginLocked($email)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Too many failed login attempts. Please try again after 15 minutes.');
        }

        $rules = [
            'email'        => 'required|valid_email',
            'password'     => 'required',
            'captcha_code' => [
                'rules'  => 'required|captcha',
                'errors' => [
                    'required' => 'Invalid CAPTCHA',
                    'captcha'  => 'Invalid CAPTCHA',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $password = $this->request->getPost('password');

        $user = $this->adminModel
            ->where('email', $email)
            ->first();

        if (!$user || !password_verify($password, $user['password'])) {
            $this->recordFailedLogin($email);
            return redirect()->back()->with('error', 'Invalid email or password');
        }

        $this->clearFailedLogins($email);
        $session->regenerate(true);

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

    private function loginAttemptKey(string $email): string
    {
        $identifier = strtolower(trim($email));
        $ip = $this->request->getIPAddress();

        return 'login_attempts_' . sha1($ip . '|' . $identifier);
    }

    private function isLoginLocked(string $email): bool
    {
        $attempts = cache($this->loginAttemptKey($email));

        return is_array($attempts)
            && ($attempts['count'] ?? 0) >= self::MAX_LOGIN_ATTEMPTS
            && ($attempts['locked_until'] ?? 0) > time();
    }

    private function recordFailedLogin(string $email): void
    {
        $key = $this->loginAttemptKey($email);
        $attempts = cache($key);

        if (! is_array($attempts)) {
            $attempts = ['count' => 0, 'locked_until' => 0];
        }

        $attempts['count']++;

        if ($attempts['count'] >= self::MAX_LOGIN_ATTEMPTS) {
            $attempts['locked_until'] = time() + self::LOGIN_LOCK_SECONDS;
        }

        cache()->save($key, $attempts, self::LOGIN_LOCK_SECONDS);
    }

    private function clearFailedLogins(string $email): void
    {
        cache()->delete($this->loginAttemptKey($email));
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

        return view('admin/layout/templates', [
            'title'      => 'Newspapers',
            'newspapers' => $newspapers,
            'pager'      => $pager,
            'content'    => 'admin/newspapers'
        ]);
    }


    public function addNewspaper()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {

            $rules = [
                'documents' => [
                    'rules' => 'uploaded[documents]|max_size[documents,10240]',
                    'errors' => [
                        'uploaded' => 'Please upload at least one document.',
                        'max_size' => 'Each file must be less than 10MB.'
                    ]
                ]
            ];

            if (!$this->validate($rules)) {
                return view('admin/layout/templates', [
                    'title' => 'Add Newspaper',
                    'content' => 'admin/add_newspaper',
                    'validation' => $this->validator
                ]);
            }

            // Upload files
            $uploadedFiles = $this->request->getFiles();
            $fileNames = [];

            if (!empty($uploadedFiles['documents'])) {
                foreach ($uploadedFiles['documents'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/newspapers/', $newName);
                        $fileNames[] = $newName;
                    }
                }
            }

            // Save
            $this->newspaperModel->insert([
                'title'        => $this->request->getPost('title'),
                'documents'    => json_encode($fileNames),
                'publish_date' => $this->request->getPost('publish_date'), // ✅ added
                'created_at'   => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('/newspapers')
                ->with('success', 'Newspaper uploaded successfully.');
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

            $rules = [
                'documents' => 'max_size[documents,10240]'
            ];

            if (!$this->validate($rules)) {
                return view('admin/layout/templates', [
                    'title' => 'Edit Newspaper',
                    'content' => 'admin/edit_newspaper',
                    'newspaper' => $newspaper,
                    'validation' => $this->validator
                ]);
            }

            // Existing files
            $existingDocs = json_decode($newspaper['documents'], true) ?? [];
            $uploadedFiles = $this->request->getFiles();
            $newFiles = [];

            if (!empty($uploadedFiles['documents'])) {
                foreach ($uploadedFiles['documents'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/newspapers/', $newName);
                        $newFiles[] = $newName;
                    }
                }
            }

            $mergedDocs = array_merge($existingDocs, $newFiles);

            // Update
            $this->newspaperModel->update($id, [
                'title'        => $this->request->getPost('title'),
                'documents'    => json_encode($mergedDocs),
                'publish_date' => $this->request->getPost('publish_date'), // ✅ added
                'updated_at'   => date('Y-m-d H:i:s')
            ]);

            return redirect()->to('/newspapers')
                ->with('success', 'Newspaper updated successfully.');
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

    public function events()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $perPage = 10;

        $events = $this->eventModel
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default');

        $pager = $this->eventModel->pager;

        return view('admin/layout/templates', [
            'title'   => 'Events',
            'events'  => $events,
            'pager'   => $pager,
            'content' => 'admin/events'
        ]);
    }

    public function addEvent()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {

            $rules = [
                'title'        => 'required',
                'from_date'    => 'required',
                'to_date'      => 'required',
                'event_images' => 'uploaded[event_images]|max_size[event_images,5120]',
                'event_document' => 'max_size[event_document,10240]'
            ];

            if (!$this->validate($rules)) {
                return view('admin/layout/templates', [
                    'title' => 'Add Event',
                    'content' => 'admin/add_event',
                    'validation' => $this->validator
                ]);
            }

            /** Upload Document */
            $docName = null;
            $docFile = $this->request->getFile('event_document');

            if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
                $docName = $docFile->getRandomName();
                $docFile->move(FCPATH . 'uploads/events/documents/', $docName);
            }

            /** Insert Event First */
            $this->eventModel->insert([
                'title'          => $this->request->getPost('title'),
                // 'description'    => $this->request->getPost('description'),
                'from_date'      => $this->request->getPost('from_date'),
                'to_date'        => $this->request->getPost('to_date'),
                'event_document' => $docName,
                'document_description' => $this->request->getPost('document_description'),
                'created_at'     => date('Y-m-d H:i:s')
            ]);

            $eventId = $this->eventModel->getInsertID();

            /** Upload Multiple Images With Descriptions */
            $imageFiles = $this->request->getFiles();
            $imageDescriptions = $this->request->getPost('image_descriptions');

            if (!empty($imageFiles['event_images'])) {

                foreach ($imageFiles['event_images'] as $index => $file) {

                    if ($file->isValid() && !$file->hasMoved()) {

                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/events/images/', $newName);

                        $description = $imageDescriptions[$index] ?? '';

                        $this->eventImageModel->insert([
                            'event_id' => $eventId,
                            'image' => $newName,
                            'image_description' => $description,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            return redirect()->to('/events')->with('success', 'Event added successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Add Event',
            'content' => 'admin/add_event'
        ]);
    }

    public function editEvent($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $event = $this->eventModel->find($id);

        if (!$event) {
            return redirect()->to('/events')->with('error', 'Event not found.');
        }

        $eventImages = $this->eventImageModel
                            ->where('event_id', $id)
                            ->findAll();

        if ($this->request->getMethod() === 'POST') {

            // ✅ Validation
            $rules = [
                'title'     => 'required',
                'from_date' => 'required',
                'to_date'   => 'required'
            ];

            if (!$this->validate($rules)) {
                return view('admin/layout/templates', [
                    'title' => 'Edit Event',
                    'event' => $event,
                    'eventImages' => $eventImages,
                    'content' => 'admin/edit_event',
                    'validation' => $this->validator
                ]);
            }

            // =========================
            // 1️⃣ Update Event Details
            // =========================
            $docName = $event['event_document'];
            $docFile = $this->request->getFile('event_document');

            if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {

                // delete old document
                if ($docName && file_exists(FCPATH . 'uploads/events/documents/' . $docName)) {
                    unlink(FCPATH . 'uploads/events/documents/' . $docName);
                }

                $docName = $docFile->getRandomName();
                $docFile->move(FCPATH . 'uploads/events/documents/', $docName);
            }

            $this->eventModel->update($id, [
                'title'       => $this->request->getPost('title'),
                // 'description' => $this->request->getPost('description'),
                'from_date'   => $this->request->getPost('from_date'),
                'to_date'     => $this->request->getPost('to_date'),
                'event_document' => $docName,
                'document_description' => $this->request->getPost('document_description'),
            ]);

            // =========================
            // 2️⃣ Update Existing Image Descriptions
            // =========================
            $existingDescriptions = $this->request->getPost('existing_descriptions');

            if (!empty($existingDescriptions)) {
                foreach ($existingDescriptions as $imageId => $desc) {

                    $this->eventImageModel->update($imageId, [
                        'image_description' => $desc
                    ]);
                }
            }

            // =========================
            // 3️⃣ Upload New Images
            // =========================
            $imageFiles = $this->request->getFiles();
            $imageDescriptions = $this->request->getPost('image_descriptions');

            if (!empty($imageFiles['event_images'])) {

                foreach ($imageFiles['event_images'] as $index => $file) {

                    if ($file->isValid() && !$file->hasMoved()) {

                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/events/images/', $newName);

                        $description = $imageDescriptions[$index] ?? '';

                        $this->eventImageModel->insert([
                            'event_id' => $id,
                            'image' => $newName,
                            'image_description' => $description
                        ]);
                    }
                }
            }

            return redirect()->to('/events')->with('success', 'Event updated successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Edit Event',
            'event' => $event,
            'eventImages' => $eventImages,
            'content' => 'admin/edit_event'
        ]);
    }

    public function deleteEventImage($imageId)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $image = $this->eventImageModel->find($imageId);

        if (!$image) {
            return redirect()->back()->with('error', 'Image not found.');
        }

        // Delete physical file
        $filePath = FCPATH . 'uploads/events/images/' . $image['image'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete database record
        $this->eventImageModel->delete($imageId);

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }


    public function deleteEvent($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $event = $this->eventModel->find($id);
        if (!$event) {
            return redirect()->to('/events')->with('error', 'Event not found.');
        }

        /** Delete images */
        $images = json_decode($event['event_images'], true) ?? [];
        foreach ($images as $img) {
            $path = FCPATH . 'uploads/events/images/' . $img;
            if (is_file($path)) {
                unlink($path);
            }
        }

        /** Delete document */
        if ($event['event_document']) {
            $docPath = FCPATH . 'uploads/events/documents/' . $event['event_document'];
            if (is_file($docPath)) {
                unlink($docPath);
            }
        }

        $this->eventModel->delete($id);

        return redirect()->to('/events')->with('success', 'Event deleted successfully.');
    }

    public function visitors()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $visitors = $this->visitorModel
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('admin/layout/templates', [
            'title' => 'Visitors',
            'visitors' => $visitors,
            'pager' => $this->visitorModel->pager,
            'content' => 'admin/visitors'
        ]);
    }
    public function addVisitor()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {

            $docName = null;
            $doc = $this->request->getFile('visitor_document');

            if ($doc && $doc->isValid()) {
                $docName = $doc->getRandomName();
                $doc->move(FCPATH . 'uploads/visitors/documents/', $docName);
            }

            $this->visitorModel->insert([
                'title' => $this->request->getPost('title'),
                'from_date' => $this->request->getPost('from_date'),
                'to_date' => $this->request->getPost('to_date'),
                'visitor_document' => $docName,
                'document_description' => $this->request->getPost('document_description')
            ]);

            $visitorId = $this->visitorModel->getInsertID();

            $files = $this->request->getFiles();
            $descs = $this->request->getPost('image_descriptions');

            if (!empty($files['visitor_images'])) {
                foreach ($files['visitor_images'] as $i => $file) {
                    if ($file->isValid()) {
                        $name = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/visitors/images/', $name);

                        $this->visitorImageModel->insert([
                            'visitor_id' => $visitorId,
                            'image' => $name,
                            'image_description' => $descs[$i] ?? ''
                        ]);
                    }
                }
            }

            return redirect()->to('/visitors')->with('success', 'Visitor added successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Add Visitor',
            'content' => 'admin/add_visitor'
        ]);
    }
    public function editVisitor($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $visitor = $this->visitorModel->find($id);
        $images = $this->visitorImageModel->where('visitor_id', $id)->findAll();

        if (!$visitor) {
            return redirect()->to('/visitors');
        }

        if ($this->request->getMethod() === 'POST') {

            $docName = $visitor['visitor_document'];
            $doc = $this->request->getFile('visitor_document');

            if ($doc && $doc->isValid()) {
                if ($docName && file_exists(FCPATH.'uploads/visitors/documents/'.$docName)) {
                    unlink(FCPATH.'uploads/visitors/documents/'.$docName);
                }

                $docName = $doc->getRandomName();
                $doc->move(FCPATH.'uploads/visitors/documents/', $docName);
            }

            $this->visitorModel->update($id, [
                'title' => $this->request->getPost('title'),
                'from_date' => $this->request->getPost('from_date'),
                'to_date' => $this->request->getPost('to_date'),
                'visitor_document' => $docName,
                'document_description' => $this->request->getPost('document_description')
            ]);

            // Update existing descriptions
            $existing = $this->request->getPost('existing_descriptions');
            if ($existing) {
                foreach ($existing as $imgId => $desc) {
                    $this->visitorImageModel->update($imgId, [
                        'image_description' => $desc
                    ]);
                }
            }

            // Add new images
            $files = $this->request->getFiles();
            $descs = $this->request->getPost('image_descriptions');

            if (!empty($files['visitor_images'])) {
                foreach ($files['visitor_images'] as $i => $file) {
                    if ($file->isValid()) {
                        $name = $file->getRandomName();
                        $file->move(FCPATH.'uploads/visitors/images/', $name);

                        $this->visitorImageModel->insert([
                            'visitor_id' => $id,
                            'image' => $name,
                            'image_description' => $descs[$i] ?? ''
                        ]);
                    }
                }
            }

            return redirect()->to('/visitors')->with('success', 'Updated successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Edit Visitor',
            'visitor' => $visitor,
            'visitorImages' => $images,
            'content' => 'admin/edit_visitor'
        ]);
    }
    public function deleteVisitorImage($imageId)
    {
        $image = $this->visitorImageModel->find($imageId);
        if (!$image) {
            return redirect()->back();
        }

        $path = FCPATH.'uploads/visitors/images/'.$image['image'];
        if (file_exists($path)) {
            unlink($path);
        }

        $this->visitorImageModel->delete($imageId);

        return redirect()->back()->with('success', 'Image deleted.');
    }
    public function deleteVisitor($id)
    {
        $visitor = $this->visitorModel->find($id);
        if (!$visitor) {
            return redirect()->to('/visitors');
        }

        $images = $this->visitorImageModel->where('visitor_id', $id)->findAll();

        foreach ($images as $img) {
            $path = FCPATH.'uploads/visitors/images/'.$img['image'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        if ($visitor['visitor_document']) {
            $doc = FCPATH.'uploads/visitors/documents/'.$visitor['visitor_document'];
            if (file_exists($doc)) {
                unlink($doc);
            }
        }

        $this->visitorModel->delete($id);

        return redirect()->to('/visitors')->with('success', 'Deleted successfully.');
    }
    public function gallery()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $perPage = 10;

        $galleries = $this->galleryModel
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        $pager = $this->galleryModel->pager;
        return view('admin/layout/templates', [
            'title'   => 'Gallery',
            'galleries'  => $galleries,
            'pager'   => $pager,
            'content' => 'admin/gallery'
        ]);
    }
    public function addGallery()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {

            $rules = [
                'title'        => 'required',
                'from_date'    => 'required',
                'to_date'      => 'required',
                'gallery_images' => 'uploaded[gallery_images]|max_size[gallery_images,5120]',
                'gallery_document' => 'max_size[gallery_document,10240]'
            ];

            if (!$this->validate($rules)) {
                return view('admin/layout/templates', [
                    'title' => 'Add Gallery',
                    'content' => 'admin/add_gallery',
                    'validation' => $this->validator
                ]);
            }

            // Upload Document
            $docName = null;
            $docFile = $this->request->getFile('gallery_document');

            if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
                $docName = $docFile->getRandomName();
                $docFile->move(FCPATH . 'uploads/gallery/documents/', $docName);
            }

            $this->galleryModel->insert([
                'title' => $this->request->getPost('title'),
                'from_date' => $this->request->getPost('from_date'),
                'to_date' => $this->request->getPost('to_date'),
                'gallery_document' => $docName,
                'document_description' => $this->request->getPost('document_description')
            ]);

            $galleryId = $this->galleryModel->getInsertID();
            $imageFiles = $this->request->getFiles();
            $descriptions = $this->request->getPost('image_descriptions');

            if (!empty($imageFiles['gallery_images'])) {
                foreach ($imageFiles['gallery_images'] as $index => $file) {

                    if ($file->isValid() && !$file->hasMoved()) {

                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/gallery/images/', $newName);

                        $this->galleryImageModel->insert([
                            'gallery_id' => $galleryId,
                            'image' => $newName,
                            'image_description' => $descriptions[$index] ?? ''
                        ]);
                    }
                }
            }

            return redirect()->to('/gallery')->with('success', 'Gallery added successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Add Gallery',
            'content' => 'admin/add_gallery'
        ]);
    }
    public function editGallery($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $gallery = $this->galleryModel->find($id);
        $images = $this->galleryImageModel->where('gallery_id', $id)->findAll();

        if (!$gallery) {
            return redirect()->to('/gallery')->with('error', 'Gallery not found.');
        }

        if ($this->request->getMethod() === 'POST') {

            $docName = $gallery['gallery_document'];
            $doc = $this->request->getFile('gallery_document');

            if ($doc && $doc->isValid()) {
                if ($docName && file_exists(FCPATH.'uploads/gallery/documents/'.$docName)) {
                    unlink(FCPATH.'uploads/gallery/documents/'.$docName);
                }

                $docName = $doc->getRandomName();
                $doc->move(FCPATH.'uploads/gallery/documents/', $docName);
            }

            $this->galleryModel->update($id, [
                'title' => $this->request->getPost('title'),
                'from_date' => $this->request->getPost('from_date'),
                'to_date' => $this->request->getPost('to_date'),
                'gallery_document' => $docName,
                'document_description' => $this->request->getPost('document_description')
            ]);

            // Update existing descriptions
            $existing = $this->request->getPost('existing_descriptions');
            if ($existing) {
                foreach ($existing as $imgId => $desc) {
                    $this->visitorImageModel->update($imgId, [
                        'image_description' => $desc
                    ]);
                }
            }

            // Add new images
            $files = $this->request->getFiles();
            $descs = $this->request->getPost('image_descriptions');

            if (!empty($files['gallery_images'])) {
                foreach ($files['gallery_images'] as $i => $file) {
                    if ($file->isValid()) {
                        $name = $file->getRandomName();
                        $file->move(FCPATH.'uploads/gallery/images/', $name);
                        $this->galleryImageModel->insert([
                            'gallery_id' => $id,
                            'image' => $name,
                            'image_description' => $descs[$i] ?? ''
                        ]);
                    }
                }
            }

            return redirect()->to('/gallery')->with('success', 'Updated successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Edit Gallery',
            'gallery' => $gallery,
            'galleryImages' => $images,
            'content' => 'admin/edit_gallery'
        ]);
    }
    public function deleteGalleryImage($imageId)
    {
        $image = $this->galleryImageModel->find($imageId);
        if (!$image) {
            return redirect()->back();
        }

        $path = FCPATH.'uploads/gallery/images/'.$image['image'];
        if (file_exists($path)) {
            unlink($path);
        }

        $this->galleryImageModel->delete($imageId);

        return redirect()->back()->with('success', 'Image deleted.');
    }
    public function deleteGallery($id)
    {
        $gallery = $this->galleryModel->find($id);
        if (!$gallery) {
            return redirect()->to('/gallery')->with('error', 'Gallery not found.');
        }

        $images = $this->galleryImageModel->where('gallery_id', $id)->findAll();

        foreach ($images as $img) {
            $path = FCPATH.'uploads/gallery/images/'.$img['image'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        if ($gallery['gallery_document']) {
            $doc = FCPATH.'uploads/gallery/documents/'.$gallery['gallery_document'];
            if (file_exists($doc)) {
                unlink($doc);
            }
        }

        $this->galleryModel->delete($id);

        return redirect()->to('/gallery')->with('success', 'Deleted successfully.');
    }
    public function vcsPrograms()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $perPage = 10;

        $vcprograms = $this->vcsProgramModel
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);

        $pager = $this->vcsProgramModel->pager;
        return view('admin/layout/templates', [
            'title'   => 'VCS Programs',
            'vcprograms'  => $vcprograms,
            'pager'   => $pager,
            'content' => 'admin/vcs_programs'
        ]);
    }
    public function addVcsProgram()
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        if ($this->request->getMethod() === 'POST') {

            $rules = [
                'title'            => 'required',
                'from_date'        => 'required',
                'to_date'          => 'required',
                'program_images'   => 'uploaded[program_images]|max_size[program_images,5120]',
                'program_document' => 'max_size[program_document,10240]'
            ];

            if (!$this->validate($rules)) {
                return view('admin/layout/templates', [
                    'title' => 'Add VCS Program',
                    'content' => 'admin/add_vcs_program',
                    'validation' => $this->validator
                ]);
            }

            // =========================
            // Upload Document
            // =========================
            $docName = null;
            $docFile = $this->request->getFile('program_document');

            if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
                $docName = $docFile->getRandomName();
                $docFile->move(FCPATH . 'uploads/vcs_programs/documents/', $docName);
            }

            // =========================
            // Insert Program
            // =========================
            $this->vcsProgramModel->insert([
                'title' => $this->request->getPost('title'),
                'from_date' => $this->request->getPost('from_date'),
                'to_date' => $this->request->getPost('to_date'),
                'program_document' => $docName,
                'document_description' => $this->request->getPost('document_description')
            ]);

            $programId = $this->vcsProgramModel->getInsertID();

            // =========================
            // Upload Images
            // =========================
            $imageFiles = $this->request->getFiles();
            $descriptions = $this->request->getPost('image_descriptions');

            if (!empty($imageFiles['program_images'])) {

                foreach ($imageFiles['program_images'] as $index => $file) {

                    if ($file->isValid() && !$file->hasMoved()) {

                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/vcs/images/', $newName);

                        $this->vcsProgramImageModel->insert([
                            'vcs_program_id' => $programId,
                            'image' => $newName,
                            'image_description' => $descriptions[$index] ?? ''
                        ]);
                    }
                }
            }

            return redirect()->to('/vcs-programs')
                             ->with('success', 'VCS Program added successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Add VCS Program',
            'content' => 'admin/add_vcs_program'
        ]);
    }
    public function editVcsProgram($id)
    {
        if ($redirect = $this->checkLogin()) {
            return $redirect;
        }

        $vcprograms = $this->vcsProgramModel->find($id);
        $images = $this->vcsProgramImageModel->where('vcs_program_id', $id)->findAll();

        if (!$vcprograms) {
            return redirect()->to('/vcs-programs')->with('error', 'VCS Program not found.');
        }

        if ($this->request->getMethod() === 'POST') {

            $docName = $vcprograms['program_document'];
            $doc = $this->request->getFile('program_document');

            if ($doc && $doc->isValid()) {
                if ($docName && file_exists(FCPATH.'uploads/vcs/documents/'.$docName)) {
                    unlink(FCPATH.'uploads/vcs/documents/'.$docName);
                }

                $docName = $doc->getRandomName();
                $doc->move(FCPATH.'uploads/vcs/documents/', $docName);
            }

            $this->vcsProgramModel->update($id, [
                'title' => $this->request->getPost('title'),
                'from_date' => $this->request->getPost('from_date'),
                'to_date' => $this->request->getPost('to_date'),
                'program_document' => $docName,
                'document_description' => $this->request->getPost('document_description')
            ]);

            // Update existing descriptions
            $existing = $this->request->getPost('existing_descriptions');
            if ($existing) {
                foreach ($existing as $imgId => $desc) {
                    $this->vcsProgramImageModel->update($imgId, [
                        'image_description' => $desc
                    ]);
                }
            }

            // Add new images
            $files = $this->request->getFiles();
            $descs = $this->request->getPost('image_descriptions');

            if (!empty($files['program_images'])) {
                foreach ($files['program_images'] as $i => $file) {
                    if ($file->isValid()) {
                        $name = $file->getRandomName();
                        $file->move(FCPATH.'uploads/vcs/images/', $name);
                        $this->vcsProgramImageModel->insert([
                            'vcs_program_id' => $id,
                            'image' => $name,
                            'image_description' => $descs[$i] ?? ''
                        ]);
                    }
                }
            }

            return redirect()->to('/vcs-programs')->with('success', 'Updated successfully.');
        }

        return view('admin/layout/templates', [
            'title' => 'Edit VCS Program',
            'vcsProgram' => $vcprograms,
            'vcsImages' => $images,
            'content' => 'admin/edit_vcs_program'
        ]);
    }
    public function deleteVcsProgramImage($imageId)
    {
        $image = $this->vcsProgramImageModel->find($imageId);
        if (!$image) {
            return redirect()->back();
        }

        $path = FCPATH.'uploads/vcs/images/'.$image['image'];
        if (file_exists($path)) {
            unlink($path);
        }

        $this->vcsProgramImageModel->delete($imageId);

        return redirect()->back()->with('success', 'Image deleted.');
    }
    public function deleteVcsProgram($id)
    {
        $vcsProgram = $this->vcsProgramModel->find($id);
        if (!$vcsProgram) {
            return redirect()->to('/vcs-programs')->with('error', 'VCS Program not found.');
        }

        $images = $this->vcsProgramImageModel->where('vcs_program_id', $id)->findAll();

        foreach ($images as $img) {
            $path = FCPATH.'uploads/vcs_programs/images/'.$img['image'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        if ($vcsProgram['program_document']) {
            $doc = FCPATH.'uploads/vcs_programs/documents/'.$vcsProgram['program_document'];
            if (file_exists($doc)) {
                unlink($doc);
            }
        }

        $this->vcsProgramModel->delete($id);

        return redirect()->to('/vcs-programs')->with('success', 'Deleted successfully.');
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

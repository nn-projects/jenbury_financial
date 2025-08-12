<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Http\Exception\BadRequestException;
use Cake\Utility\Text;

use Cake\Event\EventInterface;

class UploadsController extends AppController
{
   public function beforeFilter(EventInterface $event)
   {
       parent::beforeFilter($event);
       if ($this->request->getParam('action') === 'tinymceUpload') {
           if ($this->components()->has('FormProtection')) {
               $this->components()->unload('FormProtection');
           }
       }
   }

    public function ckeditorImage()
    {
        $this->request->allowMethod(['post']);
        $file = $this->request->getData('upload');

        if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
            throw new BadRequestException('No valid file uploaded.');
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'video/mp4'];
        if (!in_array($file->getClientMediaType(), $allowedTypes)) {
            throw new BadRequestException('Unsupported file type.');
        }

        $uploadDir = WWW_ROOT . 'uploads' . DS . 'content';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \RuntimeException('Could not create upload directory: ' . $uploadDir);
            }
        }

        $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $safeName = Text::uuid() . '.' . $ext;
        $fullPath = $uploadDir . DS . $safeName;
        $file->moveTo($fullPath);

        $this->set([
            'url' => '/uploads/content/' . $safeName,
            '_serialize' => ['url']
        ]);
    }

    public function tinymceUpload()
    {
        $this->request->allowMethod(['post']);
        // TinyMCE typically sends the file in a field named 'file'
        $file = $this->request->getData('file');

        $response = $this->response; // Get the response object

        if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
            return $response->withType('application/json')
                            ->withStringBody(json_encode(['error' => ['message' => 'No valid file uploaded or upload error.']]))
                            ->withStatus(400);
        }

        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'video/mp4',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];

        if (!in_array($file->getClientMediaType(), $allowedTypes)) {
             return $response->withType('application/json')
                            ->withStringBody(json_encode(['error' => ['message' => 'Unsupported file type: ' . $file->getClientMediaType()]]))
                            ->withStatus(400);
        }

        $uploadDir = WWW_ROOT . 'uploads' . DS . 'content';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                return $response->withType('application/json')
                                ->withStringBody(json_encode(['error' => ['message' => 'Could not create upload directory: ' . $uploadDir]]))
                                ->withStatus(500);
            }
        }

        $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $safeName = Text::uuid() . '.' . $ext;
        $fullPath = $uploadDir . DS . $safeName;

        try {
            $file->moveTo($fullPath);
        } catch (\Exception $e) {
            return $response->withType('application/json')
                            ->withStringBody(json_encode(['error' => ['message' => 'Could not move uploaded file: ' . $e->getMessage()]]))
                            ->withStatus(500);
        }

        // TinyMCE expects a JSON response with a "location" key
        return $response->withType('application/json')
                        ->withStringBody(json_encode(['location' => '/uploads/content/' . $safeName]));
    }
}

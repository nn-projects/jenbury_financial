<?php
declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Event\EventInterface;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\InternalErrorException;
use Cake\Validation\Validator;
use Laminas\Diactoros\UploadedFile;
use Cake\Log\Log;
use RuntimeException;

class FileUploadBehavior extends Behavior
{
    protected array $_defaultConfig = [
        'fields' => [],
        'path' => null,  // Will be set in initialize
        'allowedTypes' => ['image/jpeg', 'image/png', 'image/gif'],
        'maxFileSize' => 5242880, // 5MB
        'maxWidth' => 2048,
        'maxHeight' => 2048,
        'createDirectory' => true
    ];

    public function initialize(array $config): void
    {
        parent::initialize($config);

        // Set path if not configured
        if ($this->getConfig('path') === null) {
            if (!defined('WWW_ROOT')) {
                throw new RuntimeException('WWW_ROOT constant is not defined');
            }
            $this->setConfig('path', WWW_ROOT . 'img' . DS . 'uploads');
        }
        
        $path = $this->getConfig('path');
        if (!$path) {
            throw new RuntimeException('Upload path is not configured properly');
        }

        if (!file_exists($path) && $this->getConfig('createDirectory')) {
            mkdir($path, 0755, true);
        }
    }

    public function buildValidator(EventInterface $event, Validator $validator, $name): void
    {
        foreach ($this->getConfig('fields') as $field => $settings) {
            $validator->add($field, [
                'mimeType' => [
                    'rule' => ['mimeType', $this->getConfig('allowedTypes')],
                    'message' => __('Please upload a valid image file (JPEG, PNG, GIF).')
                ],
                'fileSize' => [
                    'rule' => ['fileSize', '<=', $this->getConfig('maxFileSize')],
                    'message' => __('Image must be less than 5MB.')
                ]
            ]);
        }
    }

    public function beforeSave(EventInterface $event, EntityInterface $entity, $options): void
    {
        foreach ($this->getConfig('fields') as $field => $settings) {
            if (!$entity->get($field) instanceof UploadedFile) {
                continue;
            }

            $file = $entity->get($field);
            
            try {
                // Validate MIME type
                if (!in_array($file->getClientMediaType(), $this->getConfig('allowedTypes'))) {
                    throw new RuntimeException(__('Invalid file type. Only JPEG, PNG and GIF are allowed.'));
                }

                // Generate secure filename
                $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
                $filename = $this->generateSecureFilename($ext);
                
                // Move file to final location
                $configuredPath = $this->getConfig('path'); // Get configured path
                $targetPath = $configuredPath . DS . $filename;
                Log::debug("FileUploadBehavior: Configured path: " . $configuredPath); // Log configured path
                Log::debug("FileUploadBehavior: Target path for move: " . $targetPath); // Log target path
                $file->moveTo($targetPath);

                // Validate image dimensions
                $imageSize = getimagesize($targetPath);
                if ($imageSize === false) {
                    unlink($targetPath);
                    throw new RuntimeException(__('Invalid image file.'));
                }

                list($width, $height) = $imageSize;
                if ($width > $this->getConfig('maxWidth') || $height > $this->getConfig('maxHeight')) {
                    unlink($targetPath);
                    throw new RuntimeException(__('Image dimensions exceed maximum allowed size.'));
                }

                // Store relative path in entity
                $configuredPath = $this->getConfig('path');
                $pathParts = explode(DS, rtrim($configuredPath, DS));
                $directoryName = end($pathParts); // Get the last part of the configured path (e.g., 'courses')
                $relativePath = $directoryName . DS . $filename; // Use the dynamic directory name
                $entity->set($settings['path_field'], $relativePath);

            } catch (RuntimeException $e) {
                throw new InternalErrorException($e->getMessage());
            }
        }
    }

    protected function generateSecureFilename($extension): string
    {
        return sprintf('%s.%s', bin2hex(random_bytes(16)), strtolower($extension));
    }
}
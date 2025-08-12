<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Event\EventInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class UploadComponent extends Component
{
    protected $defaultConfig = [
        'tempDir' => null,  // Will be set in initialize
        'chunkSize' => 1048576, // 1MB
    ];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        
        // Set tempDir if not configured
        if ($this->getConfig('tempDir') === null) {
            if (!defined('TMP')) {
                throw new RuntimeException('TMP constant is not defined. Make sure this component is loaded after core.php');
            }
            $this->setConfig('tempDir', TMP . 'uploads');
        }

        $tempDir = $this->getConfig('tempDir');
        if (!$tempDir) {
            throw new RuntimeException('Upload tempDir is not configured properly');
        }

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Ensure chunkSize is set and is an integer
        $chunkSize = $this->getConfig('chunkSize');
        if (!is_int($chunkSize) || $chunkSize <= 0) {
            // If not set, not an int, or not positive, force the default value from the defaultConfig array
            $this->setConfig('chunkSize', $this->defaultConfig['chunkSize']);
        }
    }

    /**
     * Handle chunked file upload
     *
     * @param UploadedFileInterface $file The uploaded file
     * @param array $data Additional upload data
     * @return array Response data
     * @throws BadRequestException When upload parameters are invalid
     * @throws InternalErrorException When upload fails
     */
    public function handleChunkedUpload(UploadedFileInterface $file, array $data): array
    {
        try {
            $chunk = (int)($data['chunk'] ?? 0);
            $chunks = (int)($data['chunks'] ?? 1);
            $tempFilename = $data['upload_id'] ?? bin2hex(random_bytes(16));
            
            // Validate chunk parameters
            if ($chunk >= $chunks) {
                throw new BadRequestException('Invalid chunk parameters');
            }

            $tempPath = $this->getConfig('tempDir') . DS . $tempFilename;
            
            // Write chunk to temp file
            $out = fopen($tempPath . '.part', $chunk === 0 ? 'wb' : 'ab');
            if ($out === false) {
                throw new RuntimeException('Failed to open output stream');
            }

            $in = $file->getStream()->detach();
            if ($in === null) {
                throw new RuntimeException('Failed to open input stream');
            }

            // Try accessing config directly, then getConfig, then default, to ensure we get an int
            $chunkSize = $this->_config['chunkSize'] ?? null; // Try direct access first
            if (!is_int($chunkSize) || $chunkSize <= 0) {
                $chunkSize = $this->getConfig('chunkSize'); // Try getConfig as fallback
                if (!is_int($chunkSize) || $chunkSize <= 0) {
                    $chunkSize = 1048576; // Hardcoded default 1MB as final fallback
                }
            }

            while (!feof($in)) {
                if (fwrite($out, fread($in, $chunkSize)) === false) { // Use the validated $chunkSize
                    throw new RuntimeException('Failed to write chunk');
                }
            }

            fclose($out);
            fclose($in);

            // Check if upload is complete
            if ($chunk === $chunks - 1) {
                // Rename temp file
                rename($tempPath . '.part', $tempPath);
                
                return [
                    'complete' => true,
                    'temp_path' => $tempPath,
                    'filename' => $file->getClientFilename()
                ];
            }

            return [
                'complete' => false,
                'upload_id' => $tempFilename,
                'chunk' => $chunk,
                'chunks' => $chunks
            ];

        } catch (RuntimeException $e) {
            // Clean up any temporary files
            @unlink($tempPath . '.part');
            @unlink($tempPath);
            // Log the original exception message before re-throwing
            // \Cake\Log\Log::error('UploadComponent caught RuntimeException: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString()); // Keep commented
            throw new InternalErrorException('Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Clean up temporary files
     *
     * @param string $uploadId Upload ID to clean up
     * @return void
     */
    public function cleanup(string $uploadId): void
    {
        $tempPath = $this->getConfig('tempDir') . DS . $uploadId;
        @unlink($tempPath . '.part');
        @unlink($tempPath);
    }
}
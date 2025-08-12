<?php
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\TableRegistry;

class ClearCourseDataCommand extends Command
{
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->addOption('force', [
            'help' => 'Force the operation without confirmation.',
            'boolean' => true,
            'short' => 'f',
        ]);
        return $parser;
    }

    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out('Starting the process to clear course-related data (excluding ContentBlocks).');
        $io->hr();

        if (!$args->getOption('force')) {
            $confirmation = $io->ask('THIS IS A DESTRUCTIVE OPERATION AND WILL PERMANENTLY DELETE COURSE-RELATED DATA. ARE YOU ABSOLUTELY SURE YOU WANT TO PROCEED? (yes/no)', 'no', ['yes', 'no']);
            if (strtolower($confirmation) !== 'yes') {
                $io->abort('Operation cancelled by user.');
                return static::CODE_SUCCESS;
            }
        }

        $io->out('Step 1: Deleting course-related database records...');
        $tablesToClear = [
            'OrderItems', 'CartItems', 'UserContentProgress', 'UserModuleProgress',
            'UserCourseProgress', 'Contents', /* 'ContentBlocks', */ 'Modules', 'Courses' // ContentBlocks removed
        ];
        foreach ($tablesToClear as $tableName) {
            try {
                $table = TableRegistry::getTableLocator()->get($tableName);
                $rowCount = $table->find()->count();
                if ($rowCount > 0) {
                    $table->deleteAll([]);
                    $io->out("Successfully deleted all {$rowCount} records from {$tableName}.");
                } else {
                    $io->out("Table {$tableName} is already empty.");
                }
            } catch (\Exception $e) {
                $io->err("Error clearing table {$tableName}: " . $e->getMessage());
            }
        }
        $io->out('Course-related database records deletion step completed.');
        $io->hr();

        $io->out('Step 2: Deleting uploaded content files (associated with courses/modules)...');
        $uploadDir = WWW_ROOT . 'uploads' . DS . 'content';
        $deletedFilesCount = 0;
        $deletedFoldersCount = 0;

        if (!is_dir($uploadDir)) {
            $io->out("Upload directory '{$uploadDir}' not found or is not a directory. Skipping file deletion.");
        } else {
            $items = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($uploadDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($items as $item) {
                if ($item->isDir()) {
                    if (count(scandir($item->getRealPath())) == 2) { 
                        if (rmdir($item->getRealPath())) {
                            $deletedFoldersCount++;
                        } else {
                            $io->err("Could not delete empty directory: " . $item->getRealPath());
                        }
                    }
                } else {
                    if (unlink($item->getRealPath())) {
                        $deletedFilesCount++;
                    } else {
                        $io->err("Could not delete file: " . $item->getRealPath());
                    }
                }
            }
            if (is_dir($uploadDir) && count(scandir($uploadDir)) == 2) {
                if (rmdir($uploadDir)) {
                     $io->out("Successfully deleted the main upload directory '{$uploadDir}' as it was empty.");
                } else {
                    $io->warning("Could not delete the main upload directory '{$uploadDir}'. It might not be empty or check permissions.");
                }
            }

            if ($deletedFilesCount > 0) {
                $io->out("Successfully deleted {$deletedFilesCount} files from {$uploadDir} and its subdirectories.");
            } else {
                $io->out("No files found to delete in {$uploadDir}.");
            }
            if ($deletedFoldersCount > 0) {
                $io->out("Successfully deleted {$deletedFoldersCount} empty sub-directories from {$uploadDir}.");
            }
        }
        $io->out('Uploaded content files deletion step completed.');
        $io->hr();

        $io->out('Course-related data has been cleared (ContentBlocks were preserved).');
        $io->out('Remember to clear your application cache if necessary: bin/cake cache clear_all');
        
        return static::CODE_SUCCESS;
    }
}
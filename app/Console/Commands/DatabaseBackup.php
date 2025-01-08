<?php

namespace App\Console\Commands;

use App\Services\GoogleDriveService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = ' db:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(GoogleDriveService $googleDriveService)
    {
        $files = Storage::disk('local')->files('exports');

        foreach ($files as $file) {
            $filePath = storage_path('app/' . $file);
            $fileName = basename($file);
            $googleDriveService->uploadToDrive($filePath, $fileName);
            $this->cleanUpOldBackups($filePath);
        }
    }

    /**
     *
     * @param string $backupPath
     */
    protected function cleanUpOldBackups($backupPath)
    {
        $files = Storage::disk('local')->files('backups');
        $currentDate = Carbon::now();

        foreach ($files as $file) {
            $filePath = storage_path("app/$file");
                unlink($filePath);
        }
    }
}

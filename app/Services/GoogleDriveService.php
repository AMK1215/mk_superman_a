<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAccessToken($this->getAccessToken());

        if ($this->client->isAccessTokenExpired()) {
            $this->refreshAccessToken();
        }

        $this->service = new Drive($this->client);
    }

    /**
     * Get the access token from the session or database.
     */
    public function getAccessToken()
    {
        $token = session('google_access_token');
        if (empty($token)) {
            $this->client->setAccessToken($this->fetchNewAccessToken());
            $token = $this->client->getAccessToken();
            session(['google_access_token' => $token]);
        }
        return $token;
    }

    /**
     * Use the refresh token to get a new access token.
     */
    public function fetchNewAccessToken()
    {
        $this->client->setAccessToken([
            'refresh_token' => env('GOOGLE_REFRESH_TOKEN')
        ]);
        
        return $this->client->getAccessToken();
    }

    /**
     * Refresh the access token if it's expired.
     */
    public function refreshAccessToken()
    {
        $this->client->fetchAccessTokenWithRefreshToken(env('GOOGLE_REFRESH_TOKEN'));
        session(['google_access_token' => $this->client->getAccessToken()]);
    }

    /**
     * Upload file to Google Drive.
     */
    public function uploadToDrive(string $filePath, string $fileName)
    {
        try {
            $file = new DriveFile();
            $file->setName($fileName);
            $file->setMimeType('application/sql'); // Set the MIME type for SQL backups

            $fileData = file_get_contents($filePath);
            $uploadedFile = $this->service->files->create(
                $file,
                [
                    'data' => $fileData,
                    'mimeType' => 'application/sql',
                    'uploadType' => 'multipart',
                ]
            );

            return $uploadedFile->id;
        } catch (\Exception $e) {
            Log::error('Error uploading file to Google Drive: ' . $e->getMessage());
            return null;
        }
    }
}

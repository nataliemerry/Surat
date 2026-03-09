<?php

namespace App\Services;

use Google\Client as GoogleClient;

class GoogleDriveService
{
    protected GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(config('filesystems.disks.google.clientId'));
        $this->client->setClientSecret(config('filesystems.disks.google.clientSecret'));
        $this->client->refreshToken(config('filesystems.disks.google.refreshToken'));
    }

    /**
     * Mengembalikan access token yang valid.
     * Otomatis fetch token baru menggunakan refresh token jika sudah expired.
     */
    public function getAccessToken(): string
    {
        $token = $this->client->getAccessToken();

        if ($this->client->isAccessTokenExpired()) {
            $token = $this->client->fetchAccessTokenWithRefreshToken(
                config('filesystems.disks.google.refreshToken')
            );
        }

        return $token['access_token'];
    }

    /**
     * Update config access token dengan token yang fresh, agar Storage::disk('google')
     * dan Gdrive:: helper selalu menggunakan token yang valid.
     * Panggil ini sebelum setiap operasi Drive.
     */
    public function refreshGlobalDisk(): void
    {
        config(['filesystems.disks.google.accessToken' => $this->getAccessToken()]);
    }
}

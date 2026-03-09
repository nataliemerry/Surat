<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;

class GoogleDriveController extends Controller
{
    /**
     * Konstruktor ini memastikan access token selalu fresh di config setiap kali
     * ada request yang melalui controller ini, sehingga Storage::disk('google')
     * dan Gdrive:: helper dapat bekerja tanpa perlu mengganti token manual.
     */
    public function __construct(private readonly GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService->refreshGlobalDisk();
    }
}
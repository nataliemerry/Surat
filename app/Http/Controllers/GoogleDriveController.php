<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;

class GoogleDriveController extends Controller
{
    public function __construct(private readonly GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService->refreshGlobalDisk();
    }
}
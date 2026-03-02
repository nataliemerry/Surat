<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Surat;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard/Index', [
            'surat' => \App\Models\Surat::query()
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($surat) => [
                    'id' => $surat->id,
                    'created_at' => $surat->created_at->format('d-m-Y'),
                    'kode' => $surat->kode,
                    'perihal' => $surat->perihal,
                    'tujuan' => $surat->tujuan,
                    'nomor' => $surat->nomor,
                    'type' => $surat->type,
                    'filepath' => $surat->filepath,
                    'isKonsumsi' => $surat->isKonsumsi,
                    'isPengelolaan' => $surat->isPengelolaan,
                    'isRuang'=>$surat->isRuang,
                ]),
        ]);
    }
}
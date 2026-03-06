<?php

namespace App\Services;

use App\Models\Surat;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

class SuratFileService
{
    private const TYPE_NAMES = [
        1 => 'Tugas',
        2 => 'Undangan',
        3 => 'Dinas',
    ];

    private const MONTH_NAMES = [
        1  => '01-Januari',
        2  => '02-Februari',
        3  => '03-Maret',
        4  => '04-April',
        5  => '05-Mei',
        6  => '06-Juni',
        7  => '07-Juli',
        8  => '08-Agustus',
        9  => '09-September',
        10 => '10-Oktober',
        11 => '11-November',
        12 => '12-Desember',
    ];

    public function __construct(private readonly GoogleDriveService $driveService) {}

    /**
     * Upload file baru ke Google Drive.
     * Folder dibuat otomatis sesuai hierarki: {tahun}/{bulan}/{jenis}.
     * Mengembalikan data drive untuk disimpan ke DB.
     */
    public function upload(Surat $surat, UploadedFile $file): array
    {
        $this->driveService->refreshGlobalDisk();

        $drivePath = $this->buildDrivePath($surat, $file->getClientOriginalExtension());

        // Gdrive::put creates parent folders automatically
        Gdrive::put($drivePath, $file);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $googleDisk */
        $googleDisk = Storage::disk('google');
        $googleDisk->setVisibility($drivePath, 'public');
        $url = $googleDisk->url($drivePath);

        return [
            'drive_file_id' => $drivePath,
            'link'          => $url,
        ];
    }

    /**
     * Hapus file lama lalu upload file baru (replace).
     */
    public function replace(Surat $surat, UploadedFile $file): array
    {
        $this->driveService->refreshGlobalDisk();

        if ($surat->drive_file_id) {
            try {
                Gdrive::delete($surat->drive_file_id);
            } catch (\Exception $e) {
                Log::warning("SuratFileService: gagal hapus file lama [{$surat->drive_file_id}]: " . $e->getMessage());
            }
        }

        return $this->upload($surat, $file);
    }

    /**
     * Hapus file dari Google Drive ketika surat dihapus.
     */
    public function delete(Surat $surat): void
    {
        if (!$surat->drive_file_id) {
            return;
        }

        $this->driveService->refreshGlobalDisk();

        try {
            Gdrive::delete($surat->drive_file_id);
        } catch (\Exception $e) {
            Log::warning("SuratFileService: gagal hapus file [{$surat->drive_file_id}]: " . $e->getMessage());
        }
    }

    /**
     * Build path Drive: {tahun}/{bulan}/{jenis}/{nama-file.ext}
     * Contoh: 2026/03-Maret/Tugas/b-100-33080-k1-2026_2026-03-06_perihal-surat_a3f9b2c1.pdf
     */
    private function buildDrivePath(Surat $surat, string $ext): string
    {
        $date     = $surat->created_at ?? now();
        $year     = $date->format('Y');
        $month    = self::MONTH_NAMES[(int) $date->format('n')];
        $type     = self::TYPE_NAMES[$surat->type] ?? 'Lainnya';
        $filename = $this->generateFilename($surat, $ext);

        return "{$year}/{$month}/{$type}/{$filename}";
    }

    /**
     * Generate nama file unik.
     * Format: {nomor-slug}_{YYYY-MM-DD}_{slug-perihal}_{8char-uuid}.{ext}
     */
    private function generateFilename(Surat $surat, string $ext): string
    {
        $nomor = $surat->nomor
            ? Str::slug(str_replace('/', '-', $surat->nomor))
            : 'draft';

        $date = ($surat->created_at ?? now())->format('Y-m-d');
        $slug = Str::limit(Str::slug($surat->perihal, '-'), 40, '');
        $uid  = substr((string) Str::uuid(), 0, 8);
        $ext  = strtolower($ext ?: 'pdf');

        return "{$nomor}_{$date}_{$slug}_{$uid}.{$ext}";
    }
}
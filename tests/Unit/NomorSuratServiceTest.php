<?php

namespace Tests\Unit;

use App\Models\Surat;
use App\Models\LetterSequence;
use App\Services\NomorSuratService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NomorSuratServiceTest extends TestCase
{
    use RefreshDatabase;

    private NomorSuratService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NomorSuratService();
    }

    private function makeSurat(array $attrs): Surat
    {
        return Surat::create(array_merge([
            'type'    => 1,
            'kode'    => 'TU.001',
            'perihal' => 'Test',
            'tujuan'  => 'Test',
        ], $attrs));
    }

    public function test_surat_tugas_pertama_menghasilkan_nomor_1(): void
    {
        $surat = $this->makeSurat(['type' => 1]);
        $nomor = $this->service->generate($surat);

        $this->assertSame("B/1/33080/TU.001/" . date('Y'), $nomor);
    }

    public function test_surat_tugas_kedua_increment_sequence(): void
    {
        $surat1 = $this->makeSurat(['type' => 1]);
        $this->service->generate($surat1);

        $surat2 = $this->makeSurat(['type' => 1]);
        $nomor  = $this->service->generate($surat2);

        $this->assertSame("B/2/33080/TU.001/" . date('Y'), $nomor);
    }

    public function test_surat_tugas_selalu_prefix_B_meski_isRahasia_true(): void
    {
        $surat = $this->makeSurat(['type' => 1, 'isRahasia' => true]);
        $nomor = $this->service->generate($surat);

        $this->assertStringStartsWith('B/', $nomor);
    }

    public function test_surat_undangan_biasa_prefix_B(): void
    {
        $surat = $this->makeSurat(['type' => 2, 'isRahasia' => false]);
        $nomor = $this->service->generate($surat);

        $this->assertSame("B/1/33080/TU.001/" . date('Y'), $nomor);
    }

    public function test_surat_undangan_rahasia_prefix_R(): void
    {
        $surat = $this->makeSurat(['type' => 2, 'isRahasia' => true]);
        $nomor = $this->service->generate($surat);

        $this->assertSame("R/1/33080/TU.001/" . date('Y'), $nomor);
    }

    public function test_surat_dinas_melanjutkan_sequence_gabungan_setelah_undangan(): void
    {
        $undangan = $this->makeSurat(['type' => 2, 'isRahasia' => false]);
        $this->service->generate($undangan);

        $dinas = $this->makeSurat(['type' => 3, 'isRahasia' => false]);
        $nomor = $this->service->generate($dinas);

        $this->assertSame("B/2/33080/TU.001/" . date('Y'), $nomor);
    }

    public function test_sequence_tugas_dan_gabungan_independen(): void
    {
        $tugas   = $this->makeSurat(['type' => 1]);
        $undangan = $this->makeSurat(['type' => 2, 'isRahasia' => false]);

        $nomorTugas   = $this->service->generate($tugas);
        $nomorUndangan = $this->service->generate($undangan);

        $this->assertSame("B/1/33080/TU.001/" . date('Y'), $nomorTugas);
        $this->assertSame("B/1/33080/TU.001/" . date('Y'), $nomorUndangan);
    }

    public function test_sequence_reset_saat_tahun_berganti(): void
    {
        $tahunLama = $this->makeSurat(['type' => 1]);
        $tahunLama->created_at = now()->subYear();
        $this->service->generate($tahunLama);

        $tahunBaru = $this->makeSurat(['type' => 1]);
        $nomor     = $this->service->generate($tahunBaru);

        $this->assertSame("B/1/33080/TU.001/" . date('Y'), $nomor);
    }

    public function test_offset_tugas_diterapkan_saat_sequence_dimulai(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $offset     = $reflection->getReflectionConstant('OFFSET_TUGAS')->getValue();

        $surat = $this->makeSurat(['type' => 1]);
        $nomor = $this->service->generate($surat);

        $expected = "B/" . (1 + $offset) . "/33080/TU.001/" . date('Y');
        $this->assertSame($expected, $nomor);
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\Kode;
use App\Services\NomorSuratService;
use App\Services\SuratFileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class SuratController extends Controller
{
    public function __construct(
        private readonly NomorSuratService $nomorService,
        private readonly SuratFileService $suratFileService,
    ) {}

    public function index() {}

    public function getSecondOptions($type)
    {
        $data = Kode::where('kode', 'like', "%{$type}%")
                    ->get(['id', 'kode', 'detail']);

        return response()->json($data->map(fn ($item) => [
            'value' => $item->kode,
            'text'  => "{$item->kode} - {$item->detail}",
        ]));
    }

    private function kodeOptions(): array
    {
        return Kode::query()
            ->orderBy('kode')
            ->get()
            ->map(fn ($kode) => [
                'id'    => $kode->id,
                'value' => $kode->kode,
                'text'  => "{$kode->kode} - {$kode->detail}",
            ])->toArray();
    }

    // ─── Surat Tugas ──────────────────────────────────────────────────────────

    public function formSuratTugas(): Response
    {
        return Inertia::render('Surat-Tugas/form', ['kode' => $this->kodeOptions()]);
    }

    public function storeSuratTugas(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'                => 'required|integer',
            'kode'                => 'required|string',
            'perihal'             => 'required|string',
            'tujuan'              => 'required|string',
        ]);

        $formattedNomor = DB::transaction(function () use ($validated) {
            $surat = Surat::create($validated);
            $nomor = $this->nomorService->generate($surat);
            $surat->update(['nomor' => $nomor]);
            return $nomor;
        });

        return Redirect::route('dashboard', ['type' => 1])
            ->with('success', "Nomor Surat Tugas Anda: $formattedNomor");
    }

    public function optionSuratTugas(): Response
    {
        return Inertia::render('Surat-Tugas/Index');
    }

    public function uploadSuratTugas(): Response
    {
        return Inertia::render('Surat-Tugas/surat');
    }

    /**
     * Upload file ke Drive untuk surat tugas yang sudah dibuat (via halaman upload).
     */
    public function updateSuratTugas(Request $request): RedirectResponse
    {
        $request->validate([
            'nomor' => 'required|string',
                'file'  => 'required|file|mimes:docx,pdf|max:1024',
        ],
        [
            'file.max' => 'Ukuran file tidak boleh lebih dari 1 MB.',
            'file.mimes' => 'File harus berupa PDF atau DOCX.',
        ]
        );

        $surat = Surat::where('nomor', $request->nomor)
            ->where('type', 1)
            ->first();

        if (!$surat) {
            return Redirect::back()->withErrors(['nomor' => 'Nomor Surat Tugas tersebut tidak ditemukan.']);
        }

        $file      = $request->file('file');
        $driveData = $this->suratFileService->replace($surat, $file);
        $surat->update(array_merge($driveData, [
            'original_filename' => $file->getClientOriginalName(),
        ]));

        return Redirect::route('dashboard', ['type' => 1]);
    }

    public function editSuratTugas(Surat $surat): Response
    {
        return Inertia::render('Surat-Tugas/edit', [
            'surat' => [
                'id'                => $surat->id,
                'type'              => $surat->type,
                'kode'              => $surat->kode,
                'perihal'           => $surat->perihal,
                'tujuan'            => $surat->tujuan,
                'nomor'             => $surat->nomor,
                'link'              => $surat->link,
                'original_filename' => $surat->original_filename,
            ],
            'kode' => $this->kodeOptions(),
        ]);
    }

    public function editedSuratTugas(Request $request, Surat $surat): RedirectResponse
    {
        $validated = $request->validate([
            'type'    => 'required|integer',
            'kode'    => 'required|string',
            'perihal' => 'required|string',
            'tujuan'  => 'required|string',
            'file'    => 'nullable|file|mimes:docx,pdf|max:1024',
        ],
        [
            'file.max' => 'Ukuran file tidak boleh lebih dari 1 MB.',
            'file.mimes' => 'File harus berupa PDF atau DOCX.',
        ]
        );

        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $driveData = $this->suratFileService->replace($surat, $file);
            $validated = array_merge($validated, $driveData, [
                'original_filename' => $file->getClientOriginalName(),
            ]);
        }

        unset($validated['file']);
        $surat->update($validated);

        return Redirect::route('dashboard', ['type' => 1])
            ->with('success', 'Surat berhasil diupdate!');
    }

    public function destroySuratTugas(Surat $surat): RedirectResponse
    {
        $this->suratFileService->delete($surat);
        $surat->delete();

        return Redirect::route('dashboard', ['type' => 1]);
    }

    // ─── Surat Undangan ───────────────────────────────────────────────────────

    public function formSuratUndangan(): Response
    {
        return Inertia::render('Surat-Undangan/form', ['kode' => $this->kodeOptions()]);
    }

    public function storeSuratUndangan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'                => 'required|integer',
            'kode'                => 'required|string',
            'isRahasia'           => 'required|boolean',
            'perihal'             => 'required|string',
            'tujuan'              => 'required|string',
            'isRuangan'           => 'required|boolean',
            'isKonsumsi'          => 'required|boolean',
            'isPengelolaan'       => 'required|boolean',
            'tanggal_pelaksanaan' => 'required|date',
        ]);

        [$formattedNomor, $surat] = DB::transaction(function () use ($validated) {
            $surat = Surat::create(collect($validated)->except('file')->toArray());
            $nomor = $this->nomorService->generate($surat);
            $surat->update(['nomor' => $nomor]);
            $surat->refresh();
            return [$nomor, $surat];
        });

        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $driveData = $this->suratFileService->upload($surat, $file);
            $surat->update(array_merge($driveData, [
                'original_filename' => $file->getClientOriginalName(),
            ]));
        }

        return Redirect::route('dashboard', ['type' => 2])
            ->with('success', "Nomor Surat Undangan Anda: $formattedNomor");
    }

    public function optionSuratUndangan(): Response
    {
        return Inertia::render('Surat-Undangan/Index');
    }

    public function uploadSuratUndangan(): Response
    {
        return Inertia::render('Surat-Undangan/surat');
    }

    public function updateSuratUndangan(Request $request): RedirectResponse
    {
        $request->validate([
            'nomor' => 'required|string',
                'file'  => 'required|file|mimes:docx,pdf|max:1024',
        ],
        [
            'file.max' => 'Ukuran file tidak boleh lebih dari 1 MB.',
            'file.mimes' => 'File harus berupa PDF atau DOCX.',
        ]
        );

        $surat = Surat::where('nomor', $request->nomor)
            ->where('type', 2)
            ->first();

        if (!$surat) {
            return Redirect::back()->withErrors(['nomor' => 'Nomor Surat Undangan tersebut tidak ditemukan.']);
        }

        $file      = $request->file('file');
        $driveData = $this->suratFileService->replace($surat, $file);
        $surat->update(array_merge($driveData, [
            'original_filename' => $file->getClientOriginalName(),
        ]));

        return Redirect::route('dashboard', ['type' => 2]);
    }

    public function editSuratUndangan(Surat $surat): Response
    {
        return Inertia::render('Surat-Undangan/edit', [
            'surat' => [
                'id'                  => $surat->id,
                'type'                => $surat->type,
                'kode'                => $surat->kode,
                'isRahasia'           => $surat->isRahasia,
                'perihal'             => $surat->perihal,
                'tujuan'              => $surat->tujuan,
                'isKonsumsi'          => $surat->isKonsumsi,
                'isPengelolaan'       => $surat->isPengelolaan,
                'isRuangan'           => $surat->isRuangan,
                'link'                => $surat->link,
                'original_filename'   => $surat->original_filename,
                'tanggal_pelaksanaan' => $surat->tanggal_pelaksanaan,
            ],
            'kode' => $this->kodeOptions(),
        ]);
    }

    public function editedSuratUndangan(Request $request, Surat $surat): RedirectResponse
    {
        $validated = $request->validate([
            'type'                => 'required|integer',
            'kode'                => 'required|string',
            'isRahasia'           => 'required|boolean',
            'perihal'             => 'required|string',
            'tujuan'              => 'required|string',
            'isKonsumsi'          => 'required|boolean',
            'isPengelolaan'       => 'required|boolean',
            'isRuangan'           => 'required|boolean',
            'tanggal_pelaksanaan' => 'required|date',
            'file'                => 'nullable|file|mimes:docx,pdf|max:1024',
        ],
        [
            'file.max' => 'Ukuran file tidak boleh lebih dari 1 MB.',
            'file.mimes' => 'File harus berupa PDF atau DOCX.',
        ]
        );

        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $driveData = $this->suratFileService->replace($surat, $file);
            $validated = array_merge($validated, $driveData, [
                'original_filename' => $file->getClientOriginalName(),
            ]);
        }

        unset($validated['file']);
        $surat->update($validated);

        return Redirect::route('dashboard', ['type' => 2])
            ->with('success', 'Surat berhasil diupdate!');
    }

    public function destroySuratUndangan(Surat $surat): RedirectResponse
    {
        $this->suratFileService->delete($surat);
        $surat->delete();

        return Redirect::route('dashboard', ['type' => 2]);
    }

    // ─── Surat Dinas ──────────────────────────────────────────────────────────

    public function formSuratDinas(): Response
    {
        return Inertia::render('Surat-Dinas/form', ['kode' => $this->kodeOptions()]);
    }

    public function storeSuratDinas(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'          => 'required|integer',
            'kode'          => 'required|string',
            'isRahasia'     => 'required|boolean',
            'perihal'       => 'required|string',
            'tujuan'        => 'required|string',
        ]);

        [$formattedNomor, $surat] = DB::transaction(function () use ($validated) {
            $surat = Surat::create(collect($validated)->except('file')->toArray());
            $nomor = $this->nomorService->generate($surat);
            $surat->update(['nomor' => $nomor]);
            $surat->refresh();
            return [$nomor, $surat];
        });

        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $driveData = $this->suratFileService->upload($surat, $file);
            $surat->update(array_merge($driveData, [
                'original_filename' => $file->getClientOriginalName(),
            ]));
        }

        return Redirect::route('dashboard', ['type' => 3])
            ->with('success', "Nomor Surat Dinas Anda: $formattedNomor");
    }

    public function optionSuratDinas(): Response
    {
        return Inertia::render('Surat-Dinas/Index');
    }

    public function uploadSuratDinas(): Response
    {
        return Inertia::render('Surat-Dinas/surat');
    }

    public function updateSuratDinas(Request $request): RedirectResponse
    {
        $request->validate([
            'nomor' => 'required|string',
                'file'  => 'required|file|mimes:docx,pdf|max:1024',
        ],
        [
            'file.max' => 'Ukuran file tidak boleh lebih dari 1 MB.',
            'file.mimes' => 'File harus berupa PDF atau DOCX.',
        ]
        );

        $surat = Surat::where('nomor', $request->nomor)
            ->where('type', 3)
            ->first();

        if (!$surat) {
            return Redirect::back()->withErrors(['nomor' => 'Nomor Surat Dinas tersebut tidak ditemukan.']);
        }

        $file      = $request->file('file');
        $driveData = $this->suratFileService->replace($surat, $file);
        $surat->update(array_merge($driveData, [
            'original_filename' => $file->getClientOriginalName(),
        ]));

        return Redirect::route('dashboard', ['type' => 3]);
    }

    public function editSuratDinas(Surat $surat): Response
    {
        return Inertia::render('Surat-Dinas/edit', [
            'surat' => [
                'id'                => $surat->id,
                'type'              => $surat->type,
                'kode'              => $surat->kode,
                'isRahasia'         => $surat->isRahasia,
                'perihal'           => $surat->perihal,
                'tujuan'            => $surat->tujuan,
                'link'              => $surat->link,
                'original_filename' => $surat->original_filename,
            ],
            'kode' => $this->kodeOptions(),
        ]);
    }

    public function editedSuratDinas(Request $request, Surat $surat): RedirectResponse
    {
        $validated = $request->validate([
            'type'      => 'required|integer',
            'kode'      => 'required|string',
            'isRahasia' => 'required|boolean',
            'perihal'   => 'required|string',
            'tujuan'    => 'required|string',
            'file'      => 'nullable|file|mimes:docx,pdf|max:1024',
        ]);

        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $driveData = $this->suratFileService->replace($surat, $file);
            $validated = array_merge($validated, $driveData, [
                'original_filename' => $file->getClientOriginalName(),
            ]);
        }

        unset($validated['file']);
        $surat->update($validated);

        return Redirect::route('dashboard', ['type' => 3])
            ->with('success', 'Surat berhasil diupdate!');
    }

    public function destroySuratDinas(Surat $surat): RedirectResponse
    {
        $this->suratFileService->delete($surat);
        $surat->delete();

        return Redirect::route('dashboard', ['type' => 3]);
    }
}
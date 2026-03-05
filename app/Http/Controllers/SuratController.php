<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\Kode;
use App\Services\NomorSuratService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SuratController extends Controller
{
    public function __construct(private readonly NomorSuratService $nomorService) {}

    public function index()
    {
    }

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

    public function formSuratTugas(): Response
    {
        return Inertia::render('Surat-Tugas/form', ['kode' => $this->kodeOptions()]);
    }

    public function storeSuratTugas(Request $request)
    {
        $validated = $request->validate([
            'type'          => 'required|integer',
            'kode'          => 'required|string',
            'perihal'       => 'required|string',
            'tujuan'        => 'required|string',
            'isKonsumsi'    => 'nullable|boolean',
            'isPengelolaan' => 'nullable|boolean',
            'filepath'      => 'nullable|string',
            'nomor'         => 'nullable|string',
            'link'          => 'nullable|string',
        ]);

        $formattedNomor = DB::transaction(function () use ($validated) {
            $surat = Surat::create($validated);
            $nomor = $this->nomorService->generate($surat);
            $surat->update(['nomor' => $nomor]);
            return $nomor;
        });

        return Redirect::route('dashboard', ['type' => 1])->with('success', "Nomor Surat Tugas Anda: $formattedNomor");
    }

    public function optionSuratTugas(): Response
    {
        return Inertia::render('Surat-Tugas/Index');
    }

    public function uploadSuratTugas(): Response
    {
        return Inertia::render('Surat-Tugas/surat');
    }

    public function updateSuratTugas(Request $request)
    {
        $validated = $request->validate([
            'nomor' => 'required|string',
            'file'  => 'nullable|file|mimes:docx,pdf',
        ]);

        $surat = Surat::where('nomor', $validated['nomor'])->first();

        if (!$surat) {
            return Redirect::back()->withErrors(['nomor' => 'Nomor Surat not found.']);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public');
            $surat->update([
                'filepath'          => Storage::url($filePath),
                'original_filename' => $file->getClientOriginalName(),
            ]);
        }

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
                'filepath'          => $surat->filepath,
                'original_filename' => $surat->original_filename,
            ],
            'kode' => $this->kodeOptions(),
        ]);
    }

    public function editedSuratTugas(Surat $surat): RedirectResponse
    {
        $surat->update(FacadeRequest::validate([
            'type'     => 'required|integer',
            'kode'     => 'required|string',
            'perihal'  => 'required|string',
            'tujuan'   => 'required|string',
            'nomor'    => 'required|string',
            'filepath' => 'nullable|string',
        ]));

        return Redirect::route('dashboard', ['type' => 1])->with('success', 'Surat berhasil diupdate!');
    }

    public function destroySuratTugas(Surat $surat): RedirectResponse
    {
        $surat->delete();
        return Redirect::route('dashboard', ['type' => 1]);
    }

        public function formSuratUndangan(): Response
    {
        return Inertia::render('Surat-Undangan/form', ['kode' => $this->kodeOptions()]);
    }

    public function storeSuratUndangan(Request $request)
    {
        $validated = $request->validate([
            'type'                 => 'required|integer',
            'kode'                 => 'required|string',
            'isRahasia'            => 'required|boolean',
            'perihal'              => 'required|string',
            'tujuan'               => 'required|string',
            'isRuangan'            => 'nullable|boolean',
            'isKonsumsi'           => 'nullable|boolean',
            'isPengelolaan'        => 'nullable|boolean',
            'filepath'             => 'nullable|string',
            'nomor'                => 'nullable|string',
            'link'                 => 'nullable|string',
            'tanggal_pelaksanaan'  => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public');
            $validated['filepath']          = Storage::url($filePath);
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        $formattedNomor = DB::transaction(function () use ($validated) {
            $surat = Surat::create($validated);
            $nomor = $this->nomorService->generate($surat);
            $surat->update(['nomor' => $nomor]);
            return $nomor;
        });

        return Redirect::route('dashboard', ['type' => 2])->with('success', "Nomor Surat Undangan Anda: $formattedNomor");
    }

    public function optionSuratUndangan(): Response
    {
        return Inertia::render('Surat-Undangan/Index');
    }

    public function uploadSuratUndangan(): Response
    {
        return Inertia::render('Surat-Undangan/surat');
    }

    public function updateSuratUndangan(Request $request)
    {
        $validated = $request->validate([
            'nomor' => 'required|string',
            'file'  => 'nullable|file|mimes:docx,pdf',
        ]);

        $surat = Surat::where('nomor', $validated['nomor'])->first();

        if (!$surat) {
            return Redirect::back()->withErrors(['nomor' => 'Nomor Surat not found.']);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public');
            $surat->update([
                'filepath'          => Storage::url($filePath),
                'original_filename' => $file->getClientOriginalName(),
            ]);
        }

        return Redirect::route('dashboard', ['type' => 2]);
    }

    public function editSuratUndangan(Surat $surat): Response
    {
        return Inertia::render('Surat-Undangan/edit', [
            'surat' => [
                'id'                   => $surat->id,
                'type'                 => $surat->type,
                'kode'                 => $surat->kode,
                'isRahasia'            => $surat->isRahasia,
                'perihal'              => $surat->perihal,
                'tujuan'               => $surat->tujuan,
                'isKonsumsi'           => $surat->isKonsumsi,
                'isPengelolaan'        => $surat->isPengelolaan,
                'isRuangan'            => $surat->isRuangan,
                'filepath'             => $surat->filepath,
                'original_filename'    => $surat->original_filename,
                'link'                 => $surat->link,
                'tanggal_pelaksanaan'  => $surat->tanggal_pelaksanaan,
            ],
            'kode' => $this->kodeOptions(),
        ]);
    }

    public function editedSuratTugasUndangan(Request $request, Surat $surat): RedirectResponse
    {
        $validated = $request->validate([
            'type'                 => 'required|integer',
            'kode'                 => 'required|string',
            'isRahasia'            => 'required|boolean',
            'perihal'              => 'required|string',
            'tujuan'               => 'required|string',
            'isKonsumsi'           => 'nullable|boolean',
            'isPengelolaan'        => 'nullable|boolean',
            'isRuangan'            => 'nullable|boolean',
            'nomor'                => 'nullable|string',
            'filepath'             => 'nullable|string',
            'link'                 => 'nullable|string',
            'tanggal_pelaksanaan'  => 'nullable|date',
            'file'                 => 'nullable|file|mimes:docx,pdf',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public');
            $validated['filepath']          = Storage::url($filePath);
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        unset($validated['file']);
        $surat->update($validated);

        return Redirect::route('dashboard', ['type' => 2])->with('success', 'Surat berhasil diupdate!');
    }

    public function destroySuratUndangan(Surat $surat): RedirectResponse
    {
        $surat->delete();
        return Redirect::route('dashboard', ['type' => 2]);
    }

    public function formSuratDinas(): Response
    {
        return Inertia::render('Surat-Dinas/form', ['kode' => $this->kodeOptions()]);
    }

    public function storeSuratDinas(Request $request)
    {
        $validated = $request->validate([
            'type'          => 'required|integer',
            'kode'          => 'required|string',
            'isRahasia'     => 'required|boolean',
            'perihal'       => 'required|string',
            'tujuan'        => 'required|string',
            'isKonsumsi'    => 'nullable|boolean',
            'isPengelolaan' => 'nullable|boolean',
            'filepath'      => 'nullable|string',
            'nomor'         => 'nullable|string',
            'link'          => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public');
            $validated['filepath'] = Storage::url($filePath);
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        $formattedNomor = DB::transaction(function () use ($validated) {
            $surat = Surat::create($validated);
            $nomor = $this->nomorService->generate($surat);
            $surat->update(['nomor' => $nomor]);
            return $nomor;
        });

        return Redirect::route('dashboard', ['type' => 3])->with('success', "Nomor Surat Dinas Anda: $formattedNomor");
    }

    public function optionSuratDinas(): Response
    {
        return Inertia::render('Surat-Dinas/Index');
    }

    public function uploadSuratDinas(): Response
    {
        return Inertia::render('Surat-Dinas/surat');
    }

    public function updateSuratUDinas(Request $request)
    {
        $validated = $request->validate([
            'nomor' => 'required|string',
            'file'  => 'nullable|file|mimes:docx,pdf',
        ]);

        $surat = Surat::where('nomor', $validated['nomor'])->first();

        if (!$surat) {
            return Redirect::back()->withErrors(['nomor' => 'Nomor Surat not found.']);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public');
            $surat->update([
                'filepath'          => Storage::url($filePath),
                'original_filename' => $file->getClientOriginalName(),
            ]);
        }

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
                'filepath'          => $surat->filepath,
                'original_filename' => $surat->original_filename,
                'link'              => $surat->link,
            ],
            'kode' => $this->kodeOptions(),
        ]);
    }

    public function editedSuratTugasDinas(Request $request, Surat $surat): RedirectResponse
    {
        $validated = $request->validate([
            'type'     => 'required|integer',
            'kode'     => 'required|string',
            'perihal'  => 'required|string',
            'tujuan'   => 'required|string',
            'nomor'    => 'nullable|string',
            'filepath' => 'nullable|string',
            'link'     => 'nullable|string',
            'file'     => 'nullable|file|mimes:docx,pdf',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public');
            $validated['filepath']          = Storage::url($filePath);
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        unset($validated['file']);
        $surat->update($validated);

        return Redirect::route('dashboard', ['type' => 3])->with('success', 'Surat berhasil diupdate!');
    }

    public function destroySuratDinas(Surat $surat): RedirectResponse
    {
        $surat->delete();
        return Redirect::route('dashboard', ['type' => 3]);
    }
}

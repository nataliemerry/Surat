<?php
namespace App\Http\Controllers;
use App\Models\Surat;
use App\Models\Kode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadeRequest;;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

    class SuratController extends Controller
    {

        public function index()
        {
            
        }
        public function getSecondOptions($type)
        {
            $data = Kode::where('kode', 'like', "%{$type}%")
                        ->get(['id', 'kode', 'detail']);

            $formattedData = $data->map(function ($item) {
                return [
                    'value' => $item->kode,
                    'text' => "{$item->kode} - {$item->detail}"
                ];
            });

            return response()->json($formattedData);
        }

        public function formSuratUndangan()
        {
            return Inertia::render('Surat-Undangan/Index', [
                'kode' => \App\Models\Kode::query()
                    ->orderByDesc('id')
                    ->get()
                    ->map(fn ($kode) => [
                        'id' => $kode->id,
                        'value' => $kode->kode,
                        'text' => "$kode->kode - $kode->detail",
                    ]),
            ]);
        }

        public function formSuratPengantar()
        {
            return Inertia::render('Surat-Pengantar/Index', [
                'kode' => \App\Models\Kode::query()
                    ->orderByDesc('id')
                    ->get()
                    ->map(fn ($kode) => [
                        'id' => $kode->id,
                        'value' => $kode->kode,
                        'text' => "$kode->kode - $kode->detail",
                    ]),
            ]);
        }

        public function formSuratTugas(): Response
        {
            return Inertia::render('Surat-Tugas/form', [
                'kode' => \App\Models\Kode::query()
                    ->orderByDesc('id')
                    ->get()
                    ->map(fn ($kode) => [
                        'id' => $kode->id,
                        'value' => $kode->kode,
                        'text' => "$kode->kode - $kode->detail",
                    ]),
            ]);
        }

        public function storeSuratPengantar(Request $request)
        {
            $validated = $request->validate([
                'type' => 'required|integer',
                'kode' => 'required|string',
                'perihal' => 'required|string',
                'tujuan' => 'required|string',
                'isKonsumsi' => 'nullable|boolean',
                'isPengelolaan' => 'nullable|boolean',
                'filepath' => 'nullable|string',
                'nomor' => 'nullable|string',
                'link' => 'nullable|string',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('uploads', 'public');
                $validated['filepath'] = Storage::url($filePath);
            }


            Surat::create($validated);
            
            return Redirect::route('dashboard')->with('success', 'Surat Pengantar Berhasil Dibuat.');
        }

        public function storeSuratUndangan(Request $request)
        {
            $validated = $request->validate([
                'type' => 'required|integer',
                'kode' => 'required|string',
                'perihal' => 'required|string',
                'tujuan' => 'required|string',
                'isKonsumsi' => 'nullable|boolean',
                'isPengelolaan' => 'nullable|boolean',
                'filepath' => 'nullable|string',
                'nomor' => 'nullable|string',
                'link' => 'nullable|string',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('uploads', 'public');
                $validated['filepath'] = Storage::url($filePath);
            }

            Surat::create($validated);
            
            return Redirect::route('dashboard')->with('success', 'Surat Undangan Berhasil Dibuat.');
        }

        public function updateSuratTugas(Request $request)
        {
            $validated = $request->validate([
                'nomor' => 'required|string',
                'file' => 'nullable|file|mimes:docx',
            ]);

            $surat = Surat::where('nomor', $validated['nomor'])->first();

            if (!$surat) {
                return Redirect::back()->withErrors(['nomor' => 'Nomor Surat not found.']);
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('uploads', 'public'); // Save file in 'uploads' directory
                $validated['filepath'] = Storage::url($filePath); // Store public URL in the database

                // Update the 'filepath' in the database
                $surat->update(['filepath' => $validated['filepath']]);
            }

            return Redirect::route('dashboard')->with('success', "Surat Tugas with Nomor {$validated['nomor']} has been updated.");
        }

        public function storeSuratTugas(Request $request)
        {
            $validated = $request->validate([
                'type' => 'required|integer',
                'kode' => 'required|string',
                'perihal' => 'required|string',
                'tujuan' => 'required|string',
                'isKonsumsi' => 'nullable|boolean',
                'isPengelolaan' => 'nullable|boolean',
                'filepath' => 'nullable|string',
                'nomor' => 'nullable|string',
                'link' => 'nullable|string',
            ]);

            $surat = Surat::create($validated);
            $formattedNomor = sprintf(
                'B-%s/33080/%s/%s',
                str_pad($surat->id, 3, '0', STR_PAD_LEFT),
                $surat->kode,
                date('Y', strtotime($surat->created_at))
            );
        
            $surat->update(['nomor' => $formattedNomor]);
        
            return Redirect::route('dashboard')->with('success', "Nomor Surat Tugas Anda: $formattedNomor");
        }


        public function optionSuratTugas(): Response
        {
            return Inertia::render('Surat-Tugas/Index');
        }

        public function uploadSuratTugas(): Response
        {
            return Inertia::render('Surat-Tugas/surat');
        }

        public function show(Surat $surat)
        {
            
        }

        public function editSuratTugas(Surat $surat): Response
        {
            return Inertia::render('Surat-Tugas/edit', [
                'surat' => [
                    'id' => $surat->id,
                    'type'=>$surat->type,
                    'kode' => $surat->kode,
                    'perihal' => $surat->perihal,
                    'tujuan' => $surat->tujuan,
                    'nomor' => $surat->nomor,
                    'filepath' => $surat->filepath,
                ],
                'kode' => \App\Models\Kode::query()
                    ->orderByDesc('id')
                    ->get()
                    ->map(fn($kode) => [
                        'id' => $kode->id,
                        'value' => $kode->kode,
                        'text' => "$kode->kode - $kode->detail",
                    ]),
            ]);
        }

        public function editedSuratTugas(Surat $surat): RedirectResponse
        {
            $surat->update(
                FacadeRequest::validate([
                    'type' => 'required|integer',
                    'kode' => 'required|string',
                    'perihal' => 'required|string',
                    'tujuan' => 'required|string',
                    'nomor' => 'required|string',
                    'filepath' => 'nullable|string',
                ])
            );
            return Redirect::back()->with('success', 'Sukses Mengupdate Surat Tugas');
        }

        public function editSuratUndangan(Surat $surat): Response
        {
            return Inertia::render('Surat-Tugas/edit', [
                'surat' => [
                    'id' => $surat->id,
                    'type'=>$surat->type,
                    'kode' => $surat->kode,
                    'perihal' => $surat->perihal,
                    'tujuan' => $surat->tujuan,
                    'isKonsumsi' => $surat->isKonsumsi,
                    'isPengelolaan' => $surat->isPengelolaan,
                    'isRuangan' => $surat->isRuangan,
                    'filepath' => $surat->filepath,
                    'link' => $surat->link,
                ],
                'kode' => \App\Models\Kode::query()
                    ->orderByDesc('id')
                    ->get()
                    ->map(fn($kode) => [
                        'id' => $kode->id,
                        'value' => $kode->kode,
                        'text' => "$kode->kode - $kode->detail",
                    ]),
            ]);
        }

        public function editedSuratUndangan(Surat $surat): RedirectResponse
        {
            $surat->update(
                FacadeRequest::validate([
                    'type' => 'required|integer',
                    'kode' => 'required|string',
                    'perihal' => 'required|string',
                    'tujuan' => 'required|string',
                    'isKonsumsi' => 'nullable|string',
                    'isPengelolaan' => 'nullable|string',
                    'isRuangan' => 'nullable|string',
                    'nomor' => 'nullable|string',
                    'filepath' => 'nullable|string',
                    'link' => 'nullable|string'
                ])
            );
            return Redirect::back()->with('success', 'Sukses Mengupdate Surat Undangan');
        }

        public function editSuratPengantar(Surat $surat): Response
        {
            return Inertia::render('Surat-Tugas/edit', [
                'surat' => [
                    'id' => $surat->id,
                    'type'=>$surat->type,
                    'kode' => $surat->kode,
                    'perihal' => $surat->perihal,
                    'tujuan' => $surat->tujuan,
                    'filepath' => $surat->filepath,
                    'link' => $surat->link,
                ],
                'kode' => \App\Models\Kode::query()
                    ->orderByDesc('id')
                    ->get()
                    ->map(fn($kode) => [
                        'id' => $kode->id,
                        'value' => $kode->kode,
                        'text' => "$kode->kode - $kode->detail",
                    ]),
            ]);
        }

        public function editedSuratPengantar(Surat $surat): RedirectResponse
        {
            $surat->update(
                FacadeRequest::validate([
                    'type' => 'required|integer',
                    'kode' => 'required|string',
                    'perihal' => 'required|string',
                    'tujuan' => 'required|string',
                    'nomor' => 'nullable|string',
                    'filepath' => 'nullable|string',
                    'link' => 'nullable|string'
                ])
            );
            return Redirect::back()->with('success', 'Sukses Mengupdate Surat Tugas');
        }

        public function update(Request $request, Surat $surat)
        {
            
        }

        public function destroy(Surat $surat)
        {
            
        }
    }

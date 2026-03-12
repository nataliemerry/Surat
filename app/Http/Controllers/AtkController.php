<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class AtkController extends Controller
{
    public function index(): Response
    {
        $requests = \App\Models\AtkRequest::with(['team', 'items.item'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($r) => [
                'id'             => $r->id,
                'requester_name' => $r->requester_name,
                'team'           => $r->team ? ['id' => $r->team->id, 'name' => $r->team->name] : null,
                'activity'       => $r->activity,
                'status'         => $r->status,
                'created_at'     => $r->created_at->format('d-m-Y'),
                'items'          => $r->items->map(fn($ri) => [
                    'id'           => $ri->id,
                    'item'         => $ri->item ? ['id' => $ri->item->id, 'name' => $ri->item->name, 'satuan' => $ri->item->satuan] : null,
                    'qty_requested' => $ri->qty_requested,
                    'qty_approved'  => $ri->qty_approved,
                ])->values(),
            ]);

        return Inertia::render('Atk/Index', [
            'requests' => $requests,
        ]);
    }

    public function form(): Response
    {
        return Inertia::render('Atk/form', [
            'teams'      => \App\Models\AtkTeam::orderBy('name')->get(),
            'categories' => \App\Models\AtkCategory::orderBy('name')->get(),
            'items'      => \App\Models\AtkItem::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'requester_name' => 'required|string|max:255',
            'team_id'        => 'required|integer|exists:atk_teams,id',
            'activity'       => 'required|string|max:255',
            'items'          => 'required|array|min:1',
            'items.*.item_id'       => 'required|integer|exists:atk_items,id',
            'items.*.qty_requested' => 'required|integer|min:1',
        ]);

        $request_record = \App\Models\AtkRequest::create([
            'requester_name' => $validated['requester_name'],
            'team_id'        => $validated['team_id'],
            'activity'       => $validated['activity'],
            'status'         => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            $request_record->items()->create([
                'item_id'       => $item['item_id'],
                'qty_requested' => $item['qty_requested'],
            ]);
        }

        return Redirect::route('atk.index')->with('success', 'Permintaan ATK berhasil diajukan.');
    }

    public function approve(Request $request, \App\Models\AtkRequest $atkRequest): RedirectResponse
    {
        $validated = $request->validate([
            'items'                  => 'required|array',
            'items.*.id'             => 'required|integer|exists:atk_request_items,id',
            'items.*.qty_approved'   => 'required|integer|min:0',
        ]);

        foreach ($validated['items'] as $itemData) {
            \App\Models\AtkRequestItem::where('id', $itemData['id'])
                ->where('request_id', $atkRequest->id)
                ->update(['qty_approved' => $itemData['qty_approved']]);
        }

        $atkRequest->update(['status' => 'approved']);

        return Redirect::route('atk.index')->with('success', 'Permintaan ATK telah disetujui.');
    }

    public function kelola(): Response
    {
        return Inertia::render('Atk/kelola', [
            'categories' => \App\Models\AtkCategory::withCount('items')->orderBy('name')->get()->map(fn($c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'items_count' => $c->items_count,
            ]),
            'items' => \App\Models\AtkItem::with('category')->orderBy('name')->get()->map(fn($i) => [
                'id'          => $i->id,
                'name'        => $i->name,
                'satuan'      => $i->satuan,
                'category_id' => $i->category_id,
                'category'    => $i->category ? ['id' => $i->category->id, 'name' => $i->category->name] : null,
            ]),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:atk_categories,name',
        ]);
        \App\Models\AtkCategory::create(['name' => $validated['name']]);
        return Redirect::route('atk.barang')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, \App\Models\AtkCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:atk_categories,name,' . $category->id,
        ]);
        $category->update(['name' => $validated['name']]);
        return Redirect::route('atk.barang')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(\App\Models\AtkCategory $category): RedirectResponse
    {
        $category->delete();
        return Redirect::route('atk.barang')->with('success', 'Kategori beserta barangnya berhasil dihapus.');
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:atk_categories,id',
            'name'        => 'required|string|max:255',
            'satuan'      => 'required|string|max:50',
        ]);
        \App\Models\AtkItem::create($validated);
        return Redirect::route('atk.barang')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function updateItem(Request $request, \App\Models\AtkItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:atk_categories,id',
            'name'        => 'required|string|max:255',
            'satuan'      => 'required|string|max:50',
        ]);
        $item->update($validated);
        return Redirect::route('atk.barang')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroyItem(\App\Models\AtkItem $item): RedirectResponse
    {
        $item->delete();
        return Redirect::route('atk.barang')->with('success', 'Barang berhasil dihapus.');
    }

    public function downloadExcel(\App\Models\AtkRequest $atkRequest): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $atkRequest->load('items.item');

        $bulanId = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $tanggal    = $atkRequest->updated_at;
        $tanggalJ3  = $tanggal->day . ' ' . $bulanId[$tanggal->month] . ' ' . $tanggal->year;
        $tanggalI39 = 'Kota Mungkid, ' . $tanggal->day . ' ' . $bulanId[$tanggal->month] . ' ' . $tanggal->year;

        $templatePath = storage_path('app/templates/template_atk.xlsx');
        $spreadsheet  = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheet        = $spreadsheet->getActiveSheet();

        // Tanggal di J3 dan I39
        $sheet->setCellValue('J3', $tanggalJ3);
        $sheet->setCellValue('I39', $tanggalI39);

        // Nama pemohon di I45
        $sheet->setCellValue('I45', $atkRequest->requester_name);

        // Data barang mulai baris 10
        foreach ($atkRequest->items as $index => $requestItem) {
            $row = 10 + $index;
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $requestItem->item?->name ?? '');
            $sheet->setCellValue('D' . $row, $requestItem->item?->satuan ?? '');
            $sheet->setCellValue('E' . $row, $requestItem->qty_requested);
            $sheet->setCellValue('F' . $row, $requestItem->qty_approved ?? 0);
        }

        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $atkRequest->requester_name);
        $dateStr  = $tanggal->format('Ymd');
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'atk_' . $safeName . '_' . $dateStr . '.xlsx';
        $tempDir  = storage_path('app/temp');

        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $tempPath = $tempDir . DIRECTORY_SEPARATOR . $filename;
        $writer->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }
}

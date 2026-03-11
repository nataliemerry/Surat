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
}

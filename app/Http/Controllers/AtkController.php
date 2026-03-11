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
        return Inertia::render('Atk/Index');
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

        return Redirect::route('atk.form')->with('success', 'Permintaan ATK berhasil diajukan.');
    }
}

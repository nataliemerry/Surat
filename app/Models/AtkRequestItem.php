<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtkRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'item_id',
        'qty_requested',
        'qty_approved',
    ];

    public function request()
    {
        return $this->belongsTo(AtkRequest::class, 'request_id');
    }

    public function item()
    {
        return $this->belongsTo(AtkItem::class, 'item_id');
    }
}

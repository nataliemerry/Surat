<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtkRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_name',
        'team_id',
        'activity',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(AtkRequestItem::class, 'request_id');
    }
}

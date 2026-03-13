<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtkRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'pegawai_id',
        'team_id',
        'activity',
        'status',
    ];

    public function pegawai()
    {
        return $this->belongsTo(\App\Models\Pegawai::class, 'pegawai_id');
    }

    public function items()
    {
        return $this->hasMany(AtkRequestItem::class, 'request_id');
    }

    public function team()
    {
        return $this->belongsTo(AtkTeam::class, 'team_id');
    }
}

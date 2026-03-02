<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'type',
        'kode',
        'perihal',
        'tujuan',
        'isKonsumsi',
        'isPengelolaan',
        'filepath',
        'nomor',
        'link',
        'isRuangan'
    ];

    public $timestamps = true;
}

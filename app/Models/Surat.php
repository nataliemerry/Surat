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
        'nomor',
        'link',
        'drive_file_id',
        'original_filename',
        'isRuangan',
        'isRahasia',
        'tanggal_pelaksanaan',
    ];

    public $timestamps = true;
}

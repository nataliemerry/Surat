<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $fillable = ['nama', 'nip'];

    public function atkRequests()
    {
        return $this->hasMany(AtkRequest::class, 'pegawai_id');
    }
}

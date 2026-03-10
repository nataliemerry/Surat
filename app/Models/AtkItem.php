<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtkItem extends Model
{
    protected $fillable = ['category_id', 'name', 'satuan'];

    public function category()
    {
        return $this->belongsTo(AtkCategory::class, 'category_id');
    }
}

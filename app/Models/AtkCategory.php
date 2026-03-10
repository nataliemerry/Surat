<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtkCategory extends Model
{
    protected $fillable = ['name'];

    public function items()
    {
        return $this->hasMany(AtkItem::class, 'category_id');
    }
}

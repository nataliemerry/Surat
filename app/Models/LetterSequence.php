<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterSequence extends Model
{
    public $timestamps = false;

    protected $fillable = ['group', 'year', 'last_sequence'];
}

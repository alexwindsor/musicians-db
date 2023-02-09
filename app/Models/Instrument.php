<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use \App\Models\Musician;

class Instrument extends Model
{
    use HasFactory;

    public $guarded = ['id'];
    public $timestamps = false;

    public function musicians() {
        return $this->belongsToMany(Musician::class);
    }

    
}

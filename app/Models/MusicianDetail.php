<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\DetailType;

class MusicianDetail extends Model
{
    use HasFactory;

    public $guarded = ['id'];



    public function musician() {
        return $this->belongsTo(Musician::class);
    }


    public function detailType() {
        return $this->hasOne(DetailType::class);
    }

}

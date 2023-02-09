<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\MusicianDetail;


class DetailType extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $guarded = ['id'];



    public function musicianDetail() {
        return $this->hasOne(MusicianDetail::class)->select(['id', 'detail_types_text']);
    }
    
}

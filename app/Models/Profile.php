<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    public $guarded = ['id'];


    public function musician() {
        return $this->belongsTo(Musician::class);
    }


}

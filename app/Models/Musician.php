<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use \App\Models\Instrument;
use \App\Models\MusicianDetail;

class Musician extends Model
{
    use HasFactory;

    public $guarded = ['id'];



    public function scopeNameFilter($query, $name) {

        if ($name ?? false) {

            $names = explode(' ', $name);

            $query->where('musicians.first_name', $names[0])
                ->orWhere('musicians.last_name', $names[0]);

            for ($i = 1; $i < count($names); $i++) {
                $query->orWhere('musicians.first_name', $names[$i])
                    ->orWhere('musicians.last_name', $names[$i]);
            }

        }
    }


    public function scopeInstrumentsFilter($query, $instruments) {

        if (count($instruments)) {

            $instruments = array_map(fn($instrument) => str_replace('_', ' ', $instrument), $instruments);

            $query->whereHas('instruments', function ($query) use ($instruments) {
                $query->whereIn('name', $instruments);
              });
        }
    }


    public function profile() {
        return $this->hasOne(Profile::class);
    }

    public function instruments() {
        return $this->belongsToMany(Instrument::class)->select(['instrument_musician.instrument_id', 'name'])->orderBy('name');
    }

    public function musicianDetails() {
        return $this->hasMany(MusicianDetail::class)->select(['detail_types_id', 'musician_details_text']);
        // return $this->hasMany(MusicianDetail::class);
    }

}

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



    public static function list($name_search, $instruments_filter, $join) {

        return Musician::with('instruments')
        ->nameFilter($name_search)
        ->instrumentsFilter($instruments_filter)
        ->$join('profiles', 'musicians.id', '=', 'profiles.musician_id')
        ->leftJoin('musician_details', 'musicians.id', '=', 'musician_details.musician_id')
        ->leftJoin('detail_types', 'musician_details.detail_types_id', '=', 'detail_types.id')
        ->select('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.id as profile_id')
        ->selectRaw('GROUP_CONCAT(`musician_details`.`musician_details_text` order by `musician_details`.`detail_types_id`) as `musician_details_text`')
        ->selectRaw('GROUP_CONCAT(`detail_types`.`detail_type_text` order by `detail_types`.`id`) as `detail_types`')
        ->groupBy('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.id')
        ->orderBy('last_name')
        ->paginate(10)
        ->withQuerystring();
    }


    public static function editData($id) {
        
        return Musician::with('instruments', 'musicianDetails')
        ->leftJoin('profiles', 'musicians.id', '=', 'profiles.musician_id')
        ->leftJoin('musician_details', 'musicians.id', '=', 'musician_details.musician_id')
        ->leftjoin('detail_types', 'musician_details.detail_types_id', '=', 'detail_types.id')
        ->select('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.text as profile_text')
        ->selectRaw('GROUP_CONCAT(`musician_details`.`id` order by `musician_details`.`detail_types_id`) as `musician_details_id`')
        ->selectRaw('GROUP_CONCAT(`musician_details`.`detail_types_id` order by `detail_types`.`id`) as `musician_detail_types_ids`')
        ->selectRaw('GROUP_CONCAT(`musician_details`.`musician_details_text` order by `musician_details`.`detail_types_id`) as `musician_details_text`')
        ->groupBy('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.text')
        ->find($id);
    }


    public static function update_musician_detail(array $musician_details_id, array $musician_detail_types_ids, array $musician_details_text): void
    {
        for ($i = 0; $i < count($musician_details_id); $i++) {
            if (strlen($musician_details_text[$i]) > 0 && intval($musician_detail_types_ids[$i]) > 0) {
                MusicianDetail::where('id', $musician_details_id[$i])
                    ->update([
                        'detail_types_id' => $musician_detail_types_ids[$i], 
                        'musician_details_text' => $musician_details_text[$i]
                    ]);
            }
        }
    }


    public static function store_musician_detail(int $musician_id, array $detail_types_id, array $musician_details_text): void
    {
        for ($i = 0; $i < count($detail_types_id); $i++) {
            if (strlen($musician_details_text[$i]) > 0 && intval($detail_types_id[$i]) > 0) {
                MusicianDetail::create([
                    'musician_id' => $musician_id,
                    'detail_types_id' => $detail_types_id[$i],
                    'musician_details_text' => $musician_details_text[$i]
                ]);
            }
        }
    }

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

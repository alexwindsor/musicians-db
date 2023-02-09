<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Models\Musician;
use \App\Models\Instrument;
use \App\Models\Profile;
use \App\Models\MusicianDetail;
use \App\Models\DetailType;
use \Dompdf\Dompdf;

class MusicianController extends Controller
{
    public function index($pdf = false) {


        $instruments_filter = [];
        if (request('instruments')) $instruments_filter = explode('*', str_replace('_', ' ', request('instruments')));

        if (request('profile_only') === 'on') $join = 'join';
        else $join = 'leftJoin';

        $musicians = Musician::with('instruments')
                ->nameFilter(request('name'))
                ->instrumentsFilter($instruments_filter)
                ->$join('profiles', 'musicians.id', '=', 'profiles.musician_id')
                ->leftJoin('musician_details', 'musicians.id', '=', 'musician_details.musician_id')
                ->join('detail_types', 'musician_details.detail_types_id', '=', 'detail_types.id')
                ->select('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.id as profile_id')
                ->selectRaw('GROUP_CONCAT(`musician_details`.`musician_details_text` order by `musician_details`.`detail_types_id`) as `musician_details_text`')
                ->selectRaw('GROUP_CONCAT(`detail_types`.`detail_type_text` order by `detail_types`.`id`) as `detail_types`')
                ->groupBy('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.id')
                ->orderBy('last_name')
                ->paginate(10)
                ->withQuerystring();

        for ($i = 0; $i < count($musicians); $i++) {
            $musicians[$i]->musician_details_text = explode(',', $musicians[$i]->musician_details_text);
            $musicians[$i]->detail_types = explode(',', $musicians[$i]->detail_types);
        }

        $instruments = Instrument::orderBy('name')->get();

        if ($pdf) {


            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->setDefaultFont('Helvetica');
            $dompdf->setOptions($options);
            $dompdf->loadHtml($this->pdfVersion($musicians, $instruments_filter));
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream();
        } 
        else return view('home', compact('musicians', 'instruments', 'instruments_filter'));
   





    }
    private function pdfVersion($musicians, $instruments_filter) {

        $pdf = '<html lang="en"><head><title>Musicians DB</title><style>table,tr,td{border:1px solid;}table{width:100%;border-collapse:collapse;}</style></head><body>';

        $pdf .= '<h1 style="font-size:300%">Musicians DB</h1>';
        $pdf .= '<br><br>';
        $pdf .= 'Instruments: ';
        if ($instruments_filter) {
            foreach ($instruments_filter as $instrument) $pdf .= $instrument . ', ';
            $pdf = rtrim($pdf, ', ');
        }
        else $pdf .= 'all';
        $pdf .= '<br>';
        if (request('name')) $pdf .= 'Search for: "' . request('name') . '"';
        $pdf .= '<br><br>';
        $pdf .= '<table style="width:100%">';
        $pdf .= '<tr>';
        $pdf .= '<th>Name</th>';
        $pdf .= '<th>Instruments Played</th>';
        $pdf .= '<th>Contact details</th>';
        $pdf .= '</tr>';

        foreach ($musicians as $musician) {
            $pdf .= '<tr>';
            $pdf .= '<td style="padding:12px;">';
            $pdf .= $musician->first_name . ' ' . $musician->last_name;
            $pdf .= '</td>';

            $pdf .= '<td style="padding:12px;">';
            foreach ($musician->instruments as $instrument) {
                $pdf .= $instrument->name;
                $pdf .= ', ';
            }
            $pdf = rtrim($pdf, ', ');
            $pdf .= '</td>';
            $pdf .= '<td style="padding:12px;">';
            for ($i = 0; $i < count($musician->musician_details_text); $i++) {
                
                $pdf .= $musician->detail_types[$i] . ': ';
                $pdf .=  '<span style="font-size:80%">' . $musician->musician_details_text[$i] . '</span>';
                $pdf .=  '<br>';

            }
            $pdf .= '</td>';
            $pdf .= '</tr>';
            
        }


        $pdf .= '</table>';
        $pdf .= '</body></html>';

        return $pdf;
    }


    public function add() {

        $instruments = Instrument::all();
        $detail_types = DetailType::all();

        return view('add_musician', compact('instruments', 'detail_types'));
    }

    public function store() {

        $fields = request()->validate([
            'first_name' => ['required', 'min:2', 'max:32'],
            'last_name' => ['required', 'min:2', 'max:32'],
            'instrument' => ['required', 'min:1'],
            'profile_text' => ['nullable']
        ]);

        $musician = Musician::create(['first_name' => $fields['first_name'], 'last_name' => $fields['last_name']]);

        $musician->first_name = $fields['first_name'];
        $musician->last_name = $fields['last_name'];

        // attach each instrument
        foreach (request('instrument') as $instrument_id => $on)
            $musician->instruments()->attach($instrument_id);

        $this->store_musician_detail($musician->id, request('detail_types'), request('musician_detail'));


        $musician->save();

        // add the profile text if there is any
        if (strlen($fields['profile_text']) > 0) 
            $musician->profile()->create(['musician_id' => $musician->id, 'text' => $fields['profile_text']]);

        

        return redirect('/');
    }

    public function store_musician_detail($musician_id, $detail_types_id, $musician_details_text) {

        for ($i = 0; $i < count($musician_details_text); $i++) { 
            if (strlen($musician_details_text[$i]) > 0 && intval($detail_types_id[$i]) > 0) {
                MusicianDetail::create([
                    'musician_id' => $musician_id,
                    'detail_types_id' => $detail_types_id[$i], 
                    'musician_details_text' => $musician_details_text[$i]
                ]);
            }
        }



    }

    public function edit($id) {

        $musician = Musician::with('instruments', 'musicianDetails')
        ->leftJoin('profiles', 'musicians.id', '=', 'profiles.musician_id')
        ->leftJoin('musician_details', 'musicians.id', '=', 'musician_details.musician_id')
        ->join('detail_types', 'musician_details.detail_types_id', '=', 'detail_types.id')
        ->select('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.text as profile_text')
        ->selectRaw('GROUP_CONCAT(`musician_details`.`id` order by `musician_details`.`detail_types_id`) as `musician_details_id`')
        ->selectRaw('GROUP_CONCAT(`musician_details`.`musician_details_text` order by `musician_details`.`detail_types_id`) as `musician_details_text`')
        ->selectRaw('GROUP_CONCAT(`detail_types`.`detail_type_text` order by `detail_types`.`id`) as `detail_types`')
        ->groupBy('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.text')
        ->find($id);

        // explode the comma separated musician_details values into arrays
        $musician->musician_details_id = explode(',', $musician->musician_details_id);
        $musician->musician_details_text = explode(',', $musician->musician_details_text);
        $musician->detail_types = explode(',', $musician->detail_types);

        // get all the instruments that this musician doesn't play
        $instruments = Instrument::whereRaw('id not in (select instrument_id from instrument_musician where musician_id = ' . $id . ')')->get(['id', 'name']);

        // get all the detail types here for the drop down
        $detail_types = DetailType::all(['id', 'detail_type_text']);

        $page = request('page');
        // need to pass querystring filters here

        return view('edit_musician', compact('musician', 'instruments', 'detail_types', 'page'));

    }


    public function update($id) {

        $fields = request()->validate([
            'first_name' => ['required', 'min:2', 'max:32'],
            'last_name' => ['required', 'min:2', 'max:32'],
            'profile_text' => ['nullable']
        ]);

        $musician = Musician::find($id);

        $musician->first_name = $fields['first_name'];
        $musician->last_name = $fields['last_name'];
        $musician->save();

        $this->store_musician_detail($musician->id, request('detail_types'), request('musician_detail'));

        // if the profile text is empty then we delete the profile record (if it exists)
        if (strlen($fields['profile_text']) === 0 && $musician->profile) 
            $musician->profile->delete();
        // otherwise we update/insert the profile record
        elseif (strlen($fields['profile_text']) > 0) 
            $musician->profile()->upsert(['musician_id' => $id, 'text' => $fields['profile_text']], true);

        return redirect('/?page=' . request('page'));

    }


    public function removeMusicianInstrument() {
        $musician = Musician::find(request('musician_id'));
        $musician->instruments()->detach(request('instrument_id'));
    }

    public function addMusicianInstrument() {
        $musician = Musician::find(request('musician_id'));
        $musician->instruments()->attach(request('instrument_id'));
    }




    public function destroy_musician_detail() {
        MusicianDetail::destroy(request('musician_detail_id'));
    }
    


    public function getProfile($profile_id) {
        $profile = Profile::find($profile_id);
        return nl2br(htmlentities($profile->text));
    }





    public function destroy($id) {
        Musician::destroy($id);
        return redirect('/?page=' . request('page'));
    }


}

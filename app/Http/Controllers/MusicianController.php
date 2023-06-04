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
use Inertia\Inertia;

class MusicianController extends Controller
{
    public function index($pdf = false) {

        $instruments_filter = [];
        if (request('instruments')) {
            $instruments_filter = explode(' ', request('instruments'));
        } 

        $page = intval(request('page'));
        $profile_only = request('profile_only') === 'on' ? true : false;
        $name_search = request('name');

        $join = $profile_only ? 'join' : 'leftJoin';

        $musicians = Musician::with('instruments')
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
        // else return view('home', compact('musicians', 'instruments', 'instruments_filter'));

        else return Inertia::render('Index', compact('musicians', 'instruments', 'instruments_filter', 'page', 'profile_only', 'name_search'));

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

        return Inertia::render('Add', compact('instruments', 'detail_types'));
    }

    public function store() {

        // dd(request());

        $fields = request()->validate([
            'first_name' => ['required', 'min:1', 'max:32'],
            'last_name' => ['required', 'min:1', 'max:32'],
            'profile' => ['nullable'],
            'instrument' => ['array', 'required', 'min:1'],
            'new_musician_detail' => ['array', 'min:1'],
            'new_detail_types' => ['array', 'min:1'],
            'new_musician_detail.*' => ['required', 'min:1'],
            'new_detail_types.*' => ['required'],

        ]);

        $musician = Musician::create(['first_name' => $fields['first_name'], 'last_name' => $fields['last_name']]);

        $musician->first_name = $fields['first_name'];
        $musician->last_name = $fields['last_name'];

        // attach each instrument
        foreach (request('instrument') as $instrument_id => $on)
            if ($on) $musician->instruments()->attach($instrument_id);

        $this->store_musician_detail($musician->id, request('new_detail_types'), request('new_musician_detail'));

        $musician->save();

        // add the profile text if there is any
        if (strlen($fields['profile']) > 0)
            $musician->profile()->create(['musician_id' => $musician->id, 'text' => $fields['profile']]);

        return redirect('/');
    }


    public function edit($id) {

        $musician = Musician::with('instruments', 'musicianDetails')
        ->leftJoin('profiles', 'musicians.id', '=', 'profiles.musician_id')
        ->leftJoin('musician_details', 'musicians.id', '=', 'musician_details.musician_id')
        ->leftjoin('detail_types', 'musician_details.detail_types_id', '=', 'detail_types.id')
        ->select('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.text as profile_text')
        ->selectRaw('GROUP_CONCAT(`musician_details`.`id` order by `musician_details`.`detail_types_id`) as `musician_details_id`')
        ->selectRaw('GROUP_CONCAT(`musician_details`.`detail_types_id` order by `detail_types`.`id`) as `musician_detail_types_ids`')
        ->selectRaw('GROUP_CONCAT(`musician_details`.`musician_details_text` order by `musician_details`.`detail_types_id`) as `musician_details_text`')
        ->groupBy('musicians.id', 'musicians.first_name', 'musicians.last_name', 'profiles.text')
        ->find($id);

        // explode the comma separated musician_details values into arrays
        $musician->musician_details_id = explode(',', $musician->musician_details_id);
        $musician->musician_detail_types_ids = explode(',', $musician->musician_detail_types_ids);
        $musician->musician_details_text = explode(',', $musician->musician_details_text);

        // get all the instruments
        $instruments = Instrument::all();

        // we need to get an array with all the instrument ids as keys and values of true/false according to whether this musician plays those instruments, for the checkboxes in the edit page
        $musician_instrument_ids = $musician->instruments->toArray();
        $musician_instrument_ids = array_column($musician_instrument_ids, 'instrument_id');
        $instrument_checkboxes = [];
        foreach ($instruments as $instrument) {
            // $instrument_checkboxes[$instrument->id] = false;
            if (in_array($instrument->id, $musician_instrument_ids))
                $instrument_checkboxes[$instrument->id] = true;
        }


        // get all the detail types here for the drop down
        $detail_types = DetailType::all(['id', 'detail_type_text']);

        $page = request('page'); // need to pass querystring filters here

        return Inertia::render('Edit', compact('musician', 'instruments', 'instrument_checkboxes', 'detail_types', 'page'));
    }


    public function update($id) {

        // die($_SERVER['QUERY_STRING'] ?? 'non');

        $fields = request()->validate([
            'first_name' => ['required', 'min:2', 'max:32'],
            'last_name' => ['required', 'min:2', 'max:32'],
            'profile_text' => ['nullable'],
            'instrument' => ['array', 'required', 'min:1'],

            'musician_details_id' => ['array', 'present'],
            'musician_details_id.*' => ['required', 'min:1'],
            'musician_detail_types_ids' => ['array', 'present'],
            'musician_detail_types_ids.*' => ['required', 'min:1'],
            'musician_details_text' => ['array', 'present'],
            'musician_details_text.*' => ['required', 'min:1'],

            'new_musician_detail' => ['array', 'present'],
            'new_detail_types' => ['array', 'present'],
            'new_musician_detail.*' => ['required', 'min:1'],
            'new_detail_types.*' => ['required'],
        ]);

        // update the main musician row
        $musician = Musician::find($id);
        $musician->first_name = $fields['first_name'];
        $musician->last_name = $fields['last_name'];
        $musician->save();

        // attach each instrument
        $musician->instruments()->sync(array_keys($fields['instrument']));

        // update the existing contact details
        if ($fields['musician_details_id'] && $fields['musician_detail_types_ids'] && $fields['musician_details_text']) {
            $this->update_musician_detail($fields['musician_details_id'], $fields['musician_detail_types_ids'], $fields['musician_details_text']);
        }
            

        // add the new contact details
        if ($fields['new_detail_types'] && $fields['new_musician_detail']) {
            $this->store_musician_detail($musician->id, $fields['new_detail_types'], $fields['new_musician_detail']);
        }
            


        // if the profile text is empty then we delete the profile record (if it exists)
        if (strlen($fields['profile_text']) === 0 && $musician->profile)
            $musician->profile->delete();
        // otherwise we update/insert the profile record
        elseif (strlen($fields['profile_text']) > 0)
            $musician->profile()->upsert(['musician_id' => $id, 'text' => $fields['profile_text']], true);

        // pass on the querystring that has been passed on since before the previous edit page
        $querystring = isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
        return redirect('/' . $querystring);

    }

    public function addMusicianInstrument() {
        $musician = Musician::find(request('musician_id'));
        $musician->instruments()->attach(request('instrument_id'));
    }

    public function removeMusicianInstrument() {
        $musician = Musician::find(request('musician_id'));
        $musician->instruments()->detach(request('instrument_id'));
    }

    public function store_musician_detail(int $musician_id, array $detail_types_id, array $musician_details_text): void
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

    public function update_musician_detail(array $musician_details_id, array $musician_detail_types_ids, array $musician_details_text): void
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

    public function destroy_musician_detail($musician_details_id): void
    {
        MusicianDetail::destroy($musician_details_id);
    }

    public function getProfile($profile_id): string
    {
        $profile = Profile::find($profile_id);
        if (! isset($profile->text)) abort(404);
        return htmlentities($profile->text);
    }

    public function destroy($id) {
        Musician::destroy($id);
        $redirect_querystring = intval(request('page')) > 1 ? '?page=' . request('page') : '';
        return redirect('/' . $redirect_querystring);
    }


}

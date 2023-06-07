<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use \Illuminate\Support\Facades\DB;
use \App\Models\Musician;
use \App\Models\Instrument;
use \App\Models\Profile;
use \App\Models\MusicianDetail;
use \App\Models\DetailType;
use \Dompdf\Dompdf;
use \Inertia\Inertia;
use App\Http\Controllers\PdfController;

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

        $musicians = Musician::list($name_search, $instruments_filter, $join);

        for ($i = 0; $i < count($musicians); $i++) {
            $musicians[$i]->musician_details_text = explode(',', $musicians[$i]->musician_details_text);
            $musicians[$i]->detail_types = explode(',', $musicians[$i]->detail_types);
        }

        $instruments = Instrument::orderBy('name')->get();

        // are we making a pdf?
        if ($pdf) {
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->setDefaultFont('Helvetica');
            $dompdf->setOptions($options);
            $dompdf->loadHtml(PdfController::pdfVersion($musicians, $instruments_filter));
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream();
        }

        else return Inertia::render('Index', compact('musicians', 'instruments', 'instruments_filter', 'page', 'profile_only', 'name_search'));
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

        Musician::store_musician_detail($musician->id, request('new_detail_types'), request('new_musician_detail'));

        $musician->save();

        // add the profile text if there is any
        if (strlen($fields['profile']) > 0)
            $musician->profile()->create(['musician_id' => $musician->id, 'text' => $fields['profile']]);

        return redirect('/');
    }


    public function edit($id) {

        $musician = Musician::editData($id);

        if (!$musician) abort(404);

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

        // dd(request());

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
            Musician::update_musician_detail($fields['musician_details_id'], $fields['musician_detail_types_ids'], $fields['musician_details_text']);
        }

        // add the new contact details
        if ($fields['new_detail_types'] && $fields['new_musician_detail']) {
            Musician::store_musician_detail($musician->id, $fields['new_detail_types'], $fields['new_musician_detail']);
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


    public function destroy_musician_detail($musician_details_id): void
    {
        MusicianDetail::destroy($musician_details_id);
    }


    public function getProfile($profile_id): string
    {
        $profile = Profile::find($profile_id);
        return htmlentities($profile->text);
    }


    public function destroy($id) {
        if (! Musician::destroy($id)) abort(404);
        $redirect_querystring = intval(request('page')) > 1 ? '?page=' . request('page') : '';
        return redirect('/' . $redirect_querystring);
    }


}

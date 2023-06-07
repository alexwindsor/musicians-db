<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Musician;
use App\Models\MusicianDetail;
use App\Models\Profile;

class CreateEditDeleteMusicianTest extends TestCase
{

    private function createMusician(): int
    {
        $musician = new Musician();
        $musician->first_name = 'TestFirstName';
        $musician->last_name = 'TestLastName';
        $musician->save();
        $musician->instruments()->attach([1, 2]);
        
        MusicianDetail::create([
            'musician_id' => $musician->id,
            'detail_types_id' => 1,
            'musician_details_text' => '01234 456789'
        ]);

        $musician->profile()->create(['musician_id' => $musician->id, 'text' => 'abc def']);

        return $musician->id;
        
    }


    public function test_add_musician_incorrectly(): void
    {
        $response = $this->post('/add_musician', [
            'first_name' => '',
            'last_name' => '',
            'profile' => null,
            'instrument' => [],
            'new_musician_detail' => [null],
            'new_detail_types' => [null]
        ]);

        $response->assertValid(['profile']);
        $response->assertInvalid(['first_name', 'last_name', 'instrument', 'new_musician_detail.0', 'new_detail_types.0']);
    }


    public function test_add_musician_correctly(): void
    {
        $response = $this->post('add_musician', [
            'first_name' => 'Testaddmusician',
            'last_name' => 'Testaddmusician',
            'profile' => 'abc def',
            'instrument' => [1 => true, 2 => true, 3 => true],
            'new_musician_detail' => ['01234 567 789', 'a@b.c'],
            'new_detail_types' => [1, 2]
        ]);

        $response->assertValid(['first_name', 'last_name', 'instrument', 'profile', 'new_musician_detail', 'new_detail_types']);

        $musician_id = Musician::select('id')->max('id');
        Musician::destroy($musician_id);
        
    }



    public function test_edit_musician_incorrectly(): void
    {

        $musician_id = $this->createMusician();

        $response = $this->put('/update_musician/' . $musician_id, [
            'first_name' => '',
            'last_name' => '',
            'profile' => null,
            'instrument' => [],
            'musician_details_id' => [],
            'musician_detail_types_ids' => [],
            'musician_details_text' => [],
            'new_musician_detail' => [null],
            'new_detail_types' => [null],
        ]);

        $response->assertValid(['profile']);
        $response->assertInvalid(['first_name', 'last_name', 'instrument', 'new_musician_detail.0', 'new_detail_types.0']);

        Musician::destroy($musician_id);
    }


    public function test_edit_musician_correctly(): void
    {

        $musician_id = $this->createMusician();

        $response = $this->put('/update_musician/' . $musician_id, [
            'first_name' => 'Testeditmusician',
            'last_name' => 'Testeditmusician',
            'profile' => null,
            'instrument' => [3 => true, 4 => true],
            'musician_details_id' => [],
            'musician_detail_types_ids' => [],
            'musician_details_text' => [],
            'new_musician_detail' => ['20-20-20 12345678', '12 Street Road\nEngland'],
            'new_detail_types' => [3, 4],
        ]);

        $response->assertValid(['profile']);
        $response->assertValid(['first_name', 'last_name', 'instrument', 'musician_details_id.0', 'musician_detail_types_ids.0', 'musician_details_text.0', 'new_musician_detail.0', 'new_detail_types.0']);

        Musician::destroy($musician_id);
    }



    public function test_edit_then_delete_musician(): void
    {
        $musician_id = $this->createMusician();

        $response = $this->get('/edit_musician/' . $musician_id);
        $response->assertOk();
        $response = $this->delete('/delete_musician/' . $musician_id);
        $response = $this->get('/edit_musician/' . $musician_id);
        $response->assertStatus(404);
    }

    public function test_get_musician_profile(): void // I CAN'T SEE WHY THIS DOESN'T WORK
    {

        $musician_id = $this->createMusician();
        $profile_id = Profile::where('musician_id', $musician_id)->first('id');
        $response = $this->post('/get_profile/' . $profile_id);
        $response->assertSee('abc def');

        Musician::destroy($musician_id);

    }



    public function test_delete_musician_detail(): void
    {
        $musician_id = $this->createMusician();
        $musician_detail_id = MusicianDetail::where('musician_id', $musician_id)->first('id');
        $response = $this->delete('/delete_musician_detail/' . $musician_detail_id);
        $response->assertStatus(200);

        Musician::destroy($musician_id);
    }



}

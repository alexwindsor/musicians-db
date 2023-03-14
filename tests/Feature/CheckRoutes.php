<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use \App\Models\Musician;

class CheckRoutes extends TestCase
{

    protected static $new_musician;


    /** @test */
    public function check_home_page(): void
    {
        $response = $this->get('/')
            ->assertStatus(200)
            ->assertSee('Musicians DB');
    }

    /** @test */
    public function check_add_musician_page(): void
    {
        $this->get('/add_musician')
            ->assertStatus(200)
            ->assertSee('Add a Musician');
    }


//    /** @test */
//    public function check_pdf(): void
//    {
//        $this->get('/pdf')->assertStatus(200);
//    }


    /** @test */
    public function check_add_musician_post(): void
    {
        $response = $this->post('/add_musician', [
            'first_name' => 'TEST123TEST123',
            'last_name' => 'Test',
            'instrument' => [1 => 'on', 2 => 'on', 3 => 'on'],
            'profile_text' => 'hello',
            'detail_types' => [1],
            'musician_detail' => ['01234 567890']
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        self::$new_musician = Musician::where('first_name', 'TEST123TEST123')->first();
    }

    /** @test */
    public function check_get_profile_ajax_call(): void
    {
        $this->post('/get_profile/' . self::$new_musician->profile->id)
            ->assertStatus(200)
            ->assertSee('hello');
    }

    /** @test */
    public function check_edit_musician_page_call(): void
    {
        $this->get('/edit_musician/' . self::$new_musician->id)
            ->assertStatus(200)
            ->assertSee('Edit Musician')
            ->assertSee('TEST123TEST123');
    }


    /** @test */
    public function check_edit_musician_post()
    {
        $this->post('/update_musician/' . self::$new_musician->id, [
            'first_name' => 'TEST123TEST123',
            'last_name' => 'TEST123TEST123',
            'profile_text' => '',
            'detail_types' => [1],
            'musician_detail' => [null]
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');
    }

    /** @test */
    public function check_get_edited_profile_ajax_call(): void
    {
        // the profile row for this musician should have been deleted in the previous method
        $this->post('/get_profile/' . self::$new_musician->profile->id)->assertStatus(404);
    }


    /** @test */
    public function check_add_musician_instrument_ajax_call(): void
    {
        $this->post('/add_musician_instrument', [
            'musician_id' => self::$new_musician->id,
            'instrument_id' => 4
        ])
            ->assertStatus(200);

        $this->assertTrue(in_array(4, Musician::find(self::$new_musician->id)->instruments()->pluck('instrument_id')->toArray()));
    }

    /** @test */
    public function check_remove_musician_instrument_ajax_call(): void
    {
        $this->post('/remove_musician_instrument', [
            'musician_id' => self::$new_musician->id,
            'instrument_id' => 4
        ])
            ->assertStatus(200);
    }


    /** @test */
    public function check_delete_musician_post()
    {
        $this->post('/delete_musician/' . self::$new_musician->id)
            ->assertStatus(302)
            ->assertRedirect('/');
    }


    /** @test */
    public function check_get_deleted_profile_ajax_call(): void
    {
        $this->post('/get_profile/' . self::$new_musician->profile->id)->assertStatus(404);
    }



}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoutesTest extends TestCase
{

    public function test_homepage(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_add_musician(): void
    {
        $response = $this->get('/add_musician');
        $response->assertStatus(200);
    }

    public function test_nonexistent_page(): void
    {
        $response = $this->get('/nonexistent_page');
        $response->assertStatus(404);
    }

    public function test_edit_musician_with_wrong_id(): void
    {
        $response = $this->get('/edit_musician/0');
        $response->assertStatus(404);
    }

    public function test_delete_musician_with_wrong_id(): void
    {
        $response = $this->delete('/delete_musician/0');
        $response->assertStatus(404);
    }

    public function test_pdf(): void
    {
        $response = $this->get('/pdf');
        $response->assertStatus(200);
    }


}


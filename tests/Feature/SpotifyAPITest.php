<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SpotifyAPITest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        var_dump(env('APP_ENV'));

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

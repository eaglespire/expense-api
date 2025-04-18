<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SecondSampleTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/greet-users');
        $response->assertStatus(200);

        $response->assertJson([
            'status' => 'success',
            'message' => 'Greet users'
        ]);
    }
}

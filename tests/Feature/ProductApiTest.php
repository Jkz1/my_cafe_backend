<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
    public function test_index_product(): void
    {
        $response = $this->get('http://127.0.0.1:8000/api/products');

        $response->assertStatus(200);
    }
    public function test_show_product(): void
    {
        
        $response = $this->get('http://127.0.0.1:8000/api/products');

        $response->assertStatus(200);
    }
}

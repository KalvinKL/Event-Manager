<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('All Events')
            ->assertSee('Browse by Category')
            ->assertSee(route('events.index', ['category' => 'music']), false)
            ->assertSee('Get Started Free')
            ->assertDontSee('Browse Festivals First');
    }

    public function test_categories_page_keeps_navigation_labels_clear(): void
    {
        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('All Events')
            ->assertSee('Browse by Category')
            ->assertSee(route('events.index', ['category' => 'music']), false);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * The front page says who is signed in and links every example.
     */
    public function test_the_front_page_lists_the_examples_and_who_is_signed_in(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Nobody is signed in')
            ->assertSee('RootAdmin role, Managers, North')
            ->assertSee('/example14/download');

        $this->actingAs(User::findOrFail(1))->get('/')->assertOk()->assertSee('Signed in as <strong>Ann</strong>', false);

        $this->get('/sign-in/2')->assertRedirect('/');
        $this->get('/')->assertSee('Signed in as <strong>Bob</strong>', false);
        $this->get('/sign-out')->assertRedirect('/');
        $this->get('/')->assertSee('Nobody is signed in');
    }
}

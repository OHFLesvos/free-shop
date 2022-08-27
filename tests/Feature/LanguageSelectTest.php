<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageSelectTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_to_language_select_page(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/languages');
    }

    public function test_show_language_select_page(): void
    {
        $response = $this->get('/languages');

        $response->assertStatus(200);
    }

    public function test_select_existing_language(): void
    {
        $response = $this->get('/languages/fr');

        $response->assertSessionHas('lang', 'fr');
        $response->assertRedirect('/');
    }

    public function test_select_non_existing_language(): void
    {
        $response = $this->get('/languages/de');

        $response->assertSessionHas('lang', 'en');
        $response->assertRedirect('/');
    }

    public function test_redirect_to_language_select_page_remember_requested_url(): void
    {
        $response = $this->get('/shop');

        $response->assertRedirect('/languages');
        $response->assertSessionHas('requested-url', 'http://localhost/shop');

        $response = $this->get('/languages/fr');

        $response->assertSessionHas('lang', 'fr');
        $response->assertRedirect('/shop');
        $response->assertSessionMissing('requested-url');

        $response = $this->get('/shop');

        $this->assertStringContainsStringIgnoringCase('<html lang="fr"', $response->getContent());
    }
}

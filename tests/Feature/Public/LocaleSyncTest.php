<?php

namespace Tests\Feature\Public;

use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
    }

    public function test_locale_switch_returns_json_for_ajax_requests(): void
    {
        $this->getJson(route('locale.switch', ['locale' => 'ar']))
            ->assertOk()
            ->assertJson([
                'locale' => 'ar',
                'dir' => 'rtl',
            ]);

        $this->assertSame('ar', session('locale'));
    }

    public function test_invalid_locale_returns_not_found(): void
    {
        $this->getJson('/locale/fr')->assertNotFound();
    }
}

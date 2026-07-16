<?php



namespace Tests\Feature\Public;



use Database\Seeders\CmsContentSeeder;

use Database\Seeders\LandingPageSeeder;

use Database\Seeders\SettingsSeeder;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;



class HomePageTest extends TestCase

{

    use RefreshDatabase;



    protected function setUp(): void

    {

        parent::setUp();



        $this->seed([SettingsSeeder::class, LandingPageSeeder::class, CmsContentSeeder::class]);

    }



    public function test_home_page_renders_react_landing(): void

    {

        $response = $this->get(route('home'));



        $response->assertOk();

        $response->assertSee('id="ghosn-landing-root"', false);

        $response->assertSee('__GHOSN_LANDING__', false);

    }

}


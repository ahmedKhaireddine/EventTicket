<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LocalizationControllerTest extends TestCase
{
    public function test_can_change_app_locale_when_we_dont_pass_lang_param_return_exeption()
    {
        // Exception
        $this->expectException(UrlGenerationException::class);

        // Action
        $response = $this->json('GET', route('locale.update'));
    }

    public function test_can_change_app_locale_when_lang_setting_is_not_fr_or_en_return_Http_code_404()
    {
        // Arrange
        $lang = 'es';

        // Action
        $response = $this->json('GET', route('locale.update', $lang));

        // Assert
        $response->assertNotFound();
    }

    public function test_can_change_app_locale_when_lang_setting_is_en_return_Http_code_200()
    {
        // Arrange
        $lang = 'en';

        // Action
        $response = $this->json('GET', route('locale.update', $lang));

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Successful change of language.'
            ]);
        $this->assertEquals($lang, App::getLocale());
    }

    public function test_can_change_app_locale_when_lang_setting_is_fr_return_Http_code_200()
    {
        // Arrange
        $lang = 'fr';

        // Action
        $response = $this->json('GET', route('locale.update', $lang));

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Changement de langue réussi.'
            ]);
        $this->assertEquals($lang, App::getLocale());
    }
}

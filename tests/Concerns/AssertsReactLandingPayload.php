<?php

namespace Tests\Concerns;

use Illuminate\Testing\TestResponse;

trait AssertsReactLandingPayload
{
    /**
     * @return array<string, mixed>
     */
    protected function landingPayloadFromResponse(TestResponse $response): array
    {
        $content = $response->getContent();

        $this->assertMatchesRegularExpression('/window\.__GHOSN_LANDING__ = /', $content);

        preg_match('/window\.__GHOSN_LANDING__ = (\{.*?\});/s', $content, $matches);

        $this->assertNotEmpty($matches[1] ?? null, 'React landing payload was not found in the homepage response.');

        return json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);
    }
}

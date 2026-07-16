<?php



namespace Tests\Concerns;



use Illuminate\Testing\TestResponse;



trait AssertsReactAdminPayload

{

    /**

     * @return array<string, mixed>

     */

    protected function adminPayloadFromResponse(TestResponse $response): array

    {

        $content = $response->getContent();



        $this->assertMatchesRegularExpression('/window\.__GHOSN_ADMIN__ = /', $content);



        preg_match('/window\.__GHOSN_ADMIN__ = (\{.*?\});/s', $content, $matches);



        $this->assertNotEmpty($matches[1] ?? null, 'React admin payload was not found in the dashboard response.');



        return json_decode($matches[1], true, 512, JSON_THROW_ON_ERROR);

    }

}


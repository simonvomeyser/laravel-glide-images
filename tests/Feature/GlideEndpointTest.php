<?php

namespace SimonVomEyser\LaravelGlideImages\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use League\Glide\Filesystem\FileNotFoundException;
use SimonVomEyser\LaravelGlideImages\Tests\TestCase;

class GlideEndpointTest extends TestCase
{
    public function test_for_existence_of_endpoint()
    {
        $glideEndpoint = config('glide-images.endpoint');

        $response = $this->get('/'.$glideEndpoint.'/images/test.jpg');

        $response->assertStatus(400); // 400 Bad Request, invalid signature
    }

    public function test_for_valid_signature_but_file_not_found_exception()
    {
        $this->withoutExceptionHandling();

        $url = glide('image/test.jpg', 100);

        $this->expectException(FileNotFoundException::class);

        $response = $this->get($url);

        $response->assertStatus(500);
    }

    public function test_for_valid_signature_and_file_exists()
    {
        $this->withoutExceptionHandling();
        $url = glide('images/test.png', 100);

        $fixtureFileContent = file_get_contents(__DIR__.'/../Fixtures/test.png');

        Storage::fake();
        Storage::disk('glide_public_path')->put('images/test.png', $fixtureFileContent);

        $response = $this->get($url);

        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_for_external_url()
    {
        // $this->withoutExceptionHandling();
        $externalUrl = 'https://raw.githubusercontent.com/simonvomeyser/laravel-glide-images/main/tests/Fixtures/test.png';
        $url = glide($externalUrl, 100);

        $response = $this->get($url);

        $response->assertStatus(200);
        $this->assertEquals('image/png', $response->headers->get('Content-Type'));

        // Assert that the remote source file is deleted after the request
        $remoteSourcePath = storage_path('app/'.config('glide-images.cache').'/.remote-sources/'.md5($externalUrl));
        $this->assertFileDoesNotExist($remoteSourcePath);
    }
}

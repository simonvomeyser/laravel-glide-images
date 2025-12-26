<?php

namespace SimonVomEyser\LaravelGlideImages\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use League\Glide\Filesystem\FileNotFoundException;
use SimonVomEyser\LaravelGlideImages\Tests\TestCase;

class GlideEndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear glide cache before each test
        $cachePath = storage_path('app/'.config('glide-images.cache'));
        if (is_dir($cachePath)) {
            $this->deleteDirectory($cachePath);
        }
    }

    protected function deleteDirectory($dir)
    {
        if (! file_exists($dir)) {
            return true;
        }

        if (! is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (! $this->deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
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
        $externalUrl = 'https://example.com/test.png';
        $url = glide($externalUrl, 100);

        \Illuminate\Support\Facades\Http::fake([
            'example.com/*' => \Illuminate\Support\Facades\Http::response(file_get_contents(__DIR__.'/../Fixtures/test.png'), 200),
        ]);

        $response = $this->get($url);

        $response->assertStatus(200);
        $this->assertEquals('image/png', $response->headers->get('Content-Type'));

        // Assert that the remote source file is deleted after the request
        $remoteSourcePath = storage_path('app/'.config('glide-images.cache').'/.remote-sources/'.md5($externalUrl));
        $this->assertFileDoesNotExist($remoteSourcePath);

        \Illuminate\Support\Facades\Http::assertSentCount(1);
    }

    public function test_remote_image_is_not_downloaded_if_cached()
    {
        $externalUrl = 'https://example.com/test-cached.png';
        $url = glide($externalUrl, 100);

        \Illuminate\Support\Facades\Http::fake([
            'example.com/*' => \Illuminate\Support\Facades\Http::response(file_get_contents(__DIR__.'/../Fixtures/test.png'), 200),
        ]);

        // First request to fill cache
        $this->get($url)->assertStatus(200);
        \Illuminate\Support\Facades\Http::assertSentCount(1);

        // Second request should hit cache and NOT download the image again
        $this->get($url)->assertStatus(200);
        \Illuminate\Support\Facades\Http::assertSentCount(1); // Still 1
    }
}

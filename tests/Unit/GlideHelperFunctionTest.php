<?php

namespace SimonVomEyser\LaravelGlideImages\Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use SimonVomEyser\LaravelGlideImages\Facades\LaravelGlideImages;
use SimonVomEyser\LaravelGlideImages\Tests\TestCase;

class GlideHelperFunctionTest extends TestCase
{
    use WithFaker;

    public function test_returns_expected_formatted_data()
    {
        $endpoint = config('glide-images.endpoint');

        $url = glide(url('/image.jpg'), [
            'w' => 100,
            'h' => 100,
        ]);

        $expectedUrl = url('/'.$endpoint.'/image.jpg?w=100&h=100');
        $this->assertStringContainsString($expectedUrl, $url);
    }

    public function test_shorthand_helpers_work()
    {
        $url = glide(url('/image.jpg'), 100);

        $expectedUrl = url('/'.config('glide-images.endpoint').'/image.jpg?w=100');
        $this->assertStringContainsString($expectedUrl, $url);
    }

    public function test_works_with_relative_urls()
    {
        $appUrl = config('app.url');
        $endpoint = config('glide-images.endpoint');

        // relative without prefix slash
        $url = glide('image.jpg', [
            'w' => 100,
            'h' => 100,
        ]);

        $expectedUrl = "$appUrl/$endpoint/image.jpg?w=100&h=100";
        $this->assertStringContainsString($expectedUrl, $url);

        // relative with prefix slash
        $url = glide('/image.jpg', [
            'w' => 100,
            'h' => 100,
        ]);

        $expectedUrl = "$appUrl/$endpoint/image.jpg?w=100&h=100";
        $this->assertStringContainsString($expectedUrl, $url);
    }

    public function test_adds_default_quality_and_fit_to_url()
    {
        $quality = config('glide-images.quality');
        $fit = config('glide-images.fit');

        $url = glide(url('/image.jpg'));

        $this->assertStringContainsString("q={$quality}", $url);
        $this->assertStringContainsString("fit={$fit}", $url);

        // overwrite the default quality and fit
        $url = glide(url('/image.jpg'), [
            'q' => 50,
            'fit' => 'crop',
        ]);

        $this->assertStringContainsString('q=50', $url);
        $this->assertStringContainsString('fit=crop', $url);

        // overwrite the config
        config(['glide-images.quality' => 10]);
        config(['glide-images.fit' => 'fill']);

        $url = glide(url('/image.jpg'));

        $this->assertStringContainsString('q=10', $url);
        $this->assertStringContainsString('fit=fill', $url);
    }

    public function test_multiple_calls_still_create_valid_url()
    {
        $url = glide(glide(url('/image.jpg'), 100), 200);

        $expectedUrl = url('/'.config('glide-images.endpoint').'/image.jpg?w=200');
        $this->assertStringContainsString($expectedUrl, $url);

        // assert that "glide" is only once in the url
        $this->assertEquals(1, substr_count($url, 'glide'));

    }

    public function test_signature_is_created_when_config_is_set()
    {
        config(['glide-images.secure' => true]);

        $url = glide(url('/image.jpg'), 100);

        $this->assertStringContainsString('s=', $url);

        // overwrite the config
        config(['glide-images.secure' => false]);

        $url = glide(url('/image.jpg'), 100);

        $this->assertStringNotContainsString('s=', $url);
    }

    public function test_using_another_domain_will_simply_return_the_input_without_modification()
    {

        $externalUrl = 'https://www.google.com/image.jpg';
        $url = glide($externalUrl, 100);

        $this->assertEquals($externalUrl, $url);
    }

    public function test_the_helper_function_works_the_same_as_the_facade()
    {
        $endpoint = config('glide-images.endpoint');

        $url = LaravelGlideImages::getUrl(url('/image.jpg'), [
            'w' => 100,
            'h' => 100,
        ]);

        $expectedUrl = url('/'.$endpoint.'/image.jpg?w=100&h=100');
        $this->assertStringContainsString($expectedUrl, $url);
    }

    public function test_existing_parameters_are_still_found_in_the_returned_url()
    {

        $url = glide(url('/image.jpg?foo=bar'), 100);

        // the url contains the original query parameter
        $this->assertStringContainsString('foo=bar', $url);
        // the url contains the glide parameter
        $this->assertStringContainsString('w=100', $url);

    }

    public function test_returning_empty_string_if_nullish_passed()
    {
        $this->assertEquals('', glide(null));
        $this->assertEquals('', glide(''));
    }
}

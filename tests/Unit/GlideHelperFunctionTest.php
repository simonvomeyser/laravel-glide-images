<?php

use SimonVomEyser\LaravelGlideImages\Facades\LaravelGlideImages;
use SimonVomEyser\LaravelGlideImages\Tests\TestCase;

class GlideHelperFunctionTest extends TestCase
{
    public function testReturnsExpectedFormattedData()
    {
        $endpoint = config('glide-images.endpoint');

        $url = glide(url('/image.jpg'), [
            'w' => 100,
            'h' => 100,
        ]);

        $expectedUrl = url('/' . $endpoint . '/image.jpg?w=100&h=100');
        $this->assertStringContainsString($expectedUrl, $url);
    }

    public function testShorthandHelpersWork()
    {
        $url = glide(url('/image.jpg'), 100);

        $expectedUrl = url('/' . config('glide-images.endpoint') . '/image.jpg?w=100');
        $this->assertStringContainsString($expectedUrl, $url);
    }

    public function testWorksWithRelativeUrls()
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

    public function testAddsDefaultQualityAndFitToUrl()
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

    public function testMultipleCallsStillCreateValidUrl()
    {
        $url = glide(glide(url('/image.jpg'), 100), 200);

        $expectedUrl = url('/' . config('glide-images.endpoint') . '/image.jpg?w=200');
        $this->assertStringContainsString($expectedUrl, $url);

        // assert that "glide" is only once in the url
        $this->assertEquals(1, substr_count($url, 'glide'));

    }
    public function testSignatureIsCreatedWhenConfigIsSet(){
        config(['glide-images.secure' => true]);

        $url = glide(url('/image.jpg'), 100);

        $this->assertStringContainsString('s=', $url);

        // overwrite the config
        config(['glide-images.secure' => false]);

        $url = glide(url('/image.jpg'), 100);

        $this->assertStringNotContainsString('s=', $url);
    }

    public function testUsingAnotherDomainWillSimplyReturnTheInputWithoutModification(){

        $externalUrl = 'https://www.google.com/image.jpg';
        $url = glide($externalUrl, 100);

        $this->assertEquals($externalUrl, $url);
    }

    public function testTheHelperFunctionWorksTheSameAsTheFacade(){
        $endpoint = config('glide-images.endpoint');

        $url = LaravelGlideImages::getUrl(url('/image.jpg'), [
            'w' => 100,
            'h' => 100,
        ]);

        $expectedUrl = url('/' . $endpoint . '/image.jpg?w=100&h=100');
        $this->assertStringContainsString($expectedUrl, $url);
    }

    public function testExistingParametersAreStillFoundInTheReturnedUrl(){

        $url = glide(url('/image.jpg?foo=bar'), 100);

        // the url contains the original query parameter
        $this->assertStringContainsString('foo=bar', $url);
        // the url contains the glide parameter
        $this->assertStringContainsString("w=100", $url);

    }
}

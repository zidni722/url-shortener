<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UrlshortenerTest extends TestCase
{
    /**
     * Test auth without API KEY
     */
    public function testCreateShortUrlWithoutApiKey()
    {
        $data = [
            "link" => "https://www.misteraladin.com/hotel/indonesia/jakarta/kuningan/manhattan-hotel-jakarta?hotel_id=560"
        ];

        $response = $this->json('POST', '/api/shortener-url/create', $data);
        $response->assertStatus(401);
        $response->assertJson(['message' => "Unauthorized"]);
    }

    /**
     * Test auth with API KEY
     */
    public function testCreateShortUrlWithApiKey()
    {
        $headers = [
            'x-api-key' => env('API_KEY')
        ];

        $data = [
            "link" => "https://www.misteraladin.com/hotel/indonesia/jakarta/kuningan/manhattan-hotel-jakarta?hotel_id=560"
        ];

        $response = $this->json('POST', '/api/shortener-url/create', $data, $headers);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'shortened_url'
            ]);
    }

    /**
     * Test create shortener url with body request is empty
     */
    public function testCreateShortUrlBodyRequestEmpty()
    {
        $headers = [
            'x-api-key' => env('API_KEY')
        ];

        $data = [];

        $response = $this->json('POST', '/api/shortener-url/create', $data, $headers);
        $response->assertStatus(400)
            ->assertJsonStructure([
                'messages'
            ]);
    }

    /**
     * Test create shortener url with url format is invalid
     */
    public function testCreateShortUrlFormatIsInvalid()
    {
        $headers = [
            'x-api-key' => env('API_KEY')
        ];

        $data = [
            "link" => "hts://www.misteraladin.com/hotel/indonesia/jakarta/kuningan/manhattan-hotel-jakarta?hotel_id=560"
        ];

        $response = $this->json('POST', '/api/shortener-url/create', $data, $headers);
        $response->assertStatus(400)
            ->assertJsonStructure([
                'messages'
            ]);
    }

    /**
     * Test edit shortener url by short code
     */
    public function testEditShortUrlByCode()
    {
        $headers = [
            'x-api-key' => env('API_KEY')
        ];

        $data = [
            "link" => "https://www.misteraladin.com/hotel/indonesia/jakarta/kuningan/manhattan-hotel-jakarta?hotel_id=560"
        ];

        $response = $this->json('POST', '/api/shortener-url/create', $data, $headers);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'shortened_url'
            ]);

        $response = $response->getOriginalContent();
        $code = explode('/', $response['shortened_url'])[4];

        $newLink = "https://www.misteraladin.com/hotel/indonesia/jakarta/kuningan/manhattan-hotel-jakarta?hotel_id=561";

        $data = [
            "link" => $newLink
        ];

        $response = $this->json('PUT', '/api/shortener-url/' . $code, $data, $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'shortened_url',
                'link'
            ]);
    }

    /**
     * Test edit shortener url by invalid short code
     */
    public function testEditShortUrlByInvalidCode()
    {
        $headers = [
            'x-api-key' => env('API_KEY')
        ];

        $data = [
            "link" => "https://www.misteraladin.com/hotel/indonesia/jakarta/kuningan/manhattan-hotel-jakarta?hotel_id=560"
        ];

        $response = $this->json('POST', '/api/shortener-url/create', $data, $headers);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'shortened_url'
            ]);

        $response = $response->getOriginalContent();
        $code = explode('/', $response['shortened_url'])[4];

        $newLink = "https://www.misteraladin.com/hotel/indonesia/jakarta/kuningan/manhattan-hotel-jakarta?hotel_id=561";

        $data = [
            "link" => $newLink
        ];

        $response = $this->json('PUT', '/api/shortener-url/' . $code . \Illuminate\Support\Str::random(1), $data, $headers);
        $response->assertStatus(404)
            ->assertJson(['messages' => "Url not found."]);
    }

    /**
     * Test delete shortener url by short code
     */
    public function testDeleteShortUrlByCode()
    {
        $headers = [
            'x-api-key' => env('API_KEY')
        ];

        $data = [
            "link" => "https://www.misteraladin.com/hotel/indonesia/jakarta/kuningan/manhattan-hotel-jakarta?hotel_id=560"
        ];

        $response = $this->json('POST', '/api/shortener-url/create', $data, $headers);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'shortened_url'
            ]);

        $response = $response->getOriginalContent();
        $code = explode('/', $response['shortened_url'])[4];
        $data = [];

        $response = $this->json('DELETE', '/api/shortener-url/' . $code, $data, $headers);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'messages'
            ]);
    }

    /**
     * Test delete shortener url by invalid short code
     */
    public function testDeleteShortUrlByInvalidCode()
    {
        $headers = [
            'x-api-key' => env('API_KEY')
        ];

        $data = [
            "link" => "https://www.misteraladin.com/hotel/indonesia/jakarta/kuningan/manhattan-hotel-jakarta?hotel_id=560"
        ];

        $response = $this->json('POST', '/api/shortener-url/create', $data, $headers);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'shortened_url'
            ]);

        $response = $response->getOriginalContent();
        $code = explode('/', $response['shortened_url'])[4];
        $data = [];

        $response = $this->json('DELETE', '/api/shortener-url/' . $code . \Illuminate\Support\Str::random(1), $data, $headers);
        $response->assertStatus(404)
            ->assertJson(['messages' => "Url not found."]);
    }
}

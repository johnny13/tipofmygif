<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GiphyService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.giphy.com/v1/gifs';

    public function __construct()
    {
        $this->apiKey = config('services.giphy.key');
    }

    public function search($query, $limit = 25, $offset = 0)
    {
        $response = Http::get("{$this->baseUrl}/search", [
            'api_key' => $this->apiKey,
            'q' => $query,
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return $response->json();
    }
}
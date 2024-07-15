<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GiphyService
{
    private $apiUrl;
    private $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.giphy.api_url');
        $this->apiKey = config('services.giphy.api_key');
    }

    public function search($query, $limit, $offset) {
        $url = "/search";

       return self::fetchData($url, $query, $limit ?? 25, $offset ?? 0);
    }

    public function searchById($id) {
        $url = "/{$id}";

       return self::fetchData($url);
    }

    private function fetchData($endpointUrl, $query=null, $limit=null, $offset=null) {
        try{
            $url = $this->apiUrl . $endpointUrl;

            $response = Http::get($url, [
                'api_key' => $this->apiKey,
                'q' => $query,
                'limit' => $limit,
                'offset' => $offset
            ]);

            $json = $response->json();
            $json = $json['data'] ?? [];
            return $json;
        }
        catch (\Throwable $th) {
            die($th);
		}
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DdfApiService {
    protected string $token;

    public function __construct() {
        $this->token = $this->getAccessToken();
    }

    protected function getAccessToken(): string {
        $response = Http::asForm()->post('https://identity.crea.ca/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => config('services.ddf.client_id'),
            'client_secret' => config('services.ddf.client_secret'),
            'scope' => 'DDFApi_Read',
        ]);

        return $response['access_token'] ?? '';
    }

    public function getOpenHouses() {
        $response = Http::withToken($this->token)
            ->get('https://ddfapi.realtor.ca/odata/v1/OpenHouse');

        return $response->json();
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DdfApiService {
    protected string $token;

    public function __construct() {
        $this->token = $this->getAccessToken();
    }


#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    protected function getAccessToken(): string {
        $response = Http::asForm()->post('https://identity.crea.ca/connect/token', [
            'grant_type' => 'client_credentials',
            'client_id' => "CXLHfDVrziCfvwgCuL8nUahC",
            'client_secret' => 'mFqMsCSPdnb5WO1gpEEtDCHH',
//            'client_id' => config('services.ddf.client_id'),
//            'client_secret' => config('services.ddf.client_secret'),
            'scope' => 'DDFApi_Read',
        ]);

        return $response['access_token'] ?? '';
    }

    public function getAllMetadataExamples() {
        $types = [
            'METADATA-SYSTEM' => ['ID' => '0', 'Format' => 'STANDARD-XML'],
            'METADATA-RESOURCE' => ['ID' => '*', 'Format' => 'STANDARD-XML'],
            'METADATA-CLASS' => ['ID' => 'Property', 'Format' => 'STANDARD-XML'],
            'METADATA-TABLE' => ['ID' => 'Property:Property', 'Format' => 'STANDARD-XML'],
            'METADATA-LOOKUP' => ['ID' => 'Property', 'Format' => 'STANDARD-XML'],
            'METADATA-LOOKUP_TYPE' => ['ID' => 'Property:PropertyType', 'Format' => 'STANDARD-XML'],
        ];

        $results = [];

        foreach ($types as $type => $params) {
            $response = Http::withToken($this->token)->get('https://data.crea.ca/Metadata.svc/GetMetadata', [
                'Type' => $type,
                'Format' => $params['Format'],
                'ID' => $params['ID'],
            ]);

            $results[$type] = [
                'status' => $response->status(),
                'body' => $response->successful() ? '✅ OK' : $response->body(),
            ];
        }

        return $results;
    }
#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    public function getOpenHouses() {
        $response = Http::withToken($this->token)
            ->get('https://ddfapi.realtor.ca/odata/v1/Property');

        return $response->json();
    }

#@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
#||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
    public function getPropertyByKey($propertyKey) {
        // تنظيف المفتاح وإضافة الهروب للعلامات المفردة
        $cleanedKey = str_replace("'", "''", $propertyKey);
        $url = "https://ddfapi.realtor.ca/odata/v1/Property('{$cleanedKey}')";

        $response = Http::withToken($this->token)
            ->acceptJson()
            ->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('DDF API request failed', [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return [
            'status' => $response->status(),
            'error' => $response->body(),
        ];
    }


    public function getPropertyKeyFromListingId(string $listingId) {
        $response = Http::withToken($this->token)->get('https://ddfapi.realtor.ca/odata/v1/Property', [
            '$filter' => "ListingID eq '$listingId'",
            '$top' => 1,
        ]);

        if ($response->successful()) {
            return $data = $response->json();
            return $data['value'][0]['Id'] ?? null; // ده هو PropertyKey المطلوب
        }

        return [
            'status' => $response->status(),
            'error' => $response->body(),
        ];
    }

}

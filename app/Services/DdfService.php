<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DdfService {
    protected $token;
    protected $metadataUrl;

    public function __construct() {
        $this->token = $this->getAccessToken();
        $this->metadataUrl = 'https://data.crea.ca/Metadata.svc/GetMetadata';
    }

    protected function getAccessToken(): string {
        return Cache::remember('ddf_access_token', 3600, function () {
            $response = Http::asForm()->post('https://identity.crea.ca/connect/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.ddf_demo.client_id'),
                'client_secret' => config('services.ddf_demo.client_secret'),
                'scope' => 'DDFApi_Read DDFApi_FullAccess'
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            throw new \Exception('Failed to get DDF access token');
        });
    }

    /**
     * جلب جميع أنواع البيانات الوصفية المتاحة
     */
    public function getAllMetadataTypes() {
        // أنواع البيانات الوصفية الأساسية
        $metadataTypes = [
            'METADATA-SYSTEM',
            'METADATA-RESOURCE',
            'METADATA-CLASS',
            'METADATA-LOOKUP',
            'METADATA-LOOKUP_TYPE'
        ];

        $results = [];

        foreach ($metadataTypes as $type) {
            $response = Http::withToken($this->token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($this->metadataUrl, [
                    'Type' => $type,
                    'ID' => '*', // للحصول على جميع السجلات
                    'Format' => 'STANDARD-XML'
                ]);

            if ($response->successful()) {
                $results[$type] = $response->body();
            } else {
                $results[$type] = [
                    'error' => $response->status(),
                    'body' => $response->body()
                ];
            }
        }

        return $results;
    }

    /**
     * جلب البيانات الوصفية لمورد معين (مثل Property)
     */
    public function getResourceMetadata(string $resource) {
        $types = [
            'METADATA-CLASS' => $resource,
            'METADATA-LOOKUP' => $resource,
            'METADATA-TABLE' => "$resource:Property" // مثال: Property:Property
        ];

        $results = [];

        foreach ($types as $type => $id) {
            $response = Http::withToken($this->token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($this->metadataUrl, [
                    'Type' => $type,
                    'ID' => $id,
                    'Format' => 'STANDARD-XML'
                ]);

            if ($response->successful()) {
                $results[$type] = $response->body();
            } else {
                $results[$type] = [
                    'error' => $response->status(),
                    'body' => $response->body()
                ];
            }
        }

        return $results;
    }
}

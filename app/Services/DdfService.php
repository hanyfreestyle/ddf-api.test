<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DdfService
{
    protected string $username;
    protected string $password;
    protected string $sessionId;   // X-SESSIONID
    protected string $baseUrl = 'https://data.crea.ca';

    public function __construct()
    {
        $this->username = 'CXLHfDVrziCfvwgCuL8nUahC';
        $this->password = 'mFqMsCSPdnb5WO1gpEEtDCHH';
        $this->sessionId = $this->loginAndCacheSession();
    }
//DEMO_CLIENT_ID=CXLHfDVrziCfvwgCuL8nUahC
//DEMO_CLIENT_SECRET=mFqMsCSPdnb5WO1gpEEtDCHH
    /**
     * يسجِّل الدخول (DigestAuth) ويحفظ X-SESSIONID فى الكاش ساعة.
     */
    protected function loginAndCacheSession(): string
    {
        return Cache::remember('ddf_session_id', 3600, function () {
            $response = Http::withDigestAuth($this->username, $this->password)
                ->accept('text/xml')
                ->get("{$this->baseUrl}/Login.svc/Login");

            if (!$response->successful()) {
                throw new \RuntimeException('DDF login failed: ' . $response->status());
            }

            // استخرج قيمة X-SESSIONID من ترويسة Set-Cookie
            $cookieHeader = collect($response->header('Set-Cookie'))
                ->first(fn ($c) => str_contains($c, 'X-SESSIONID'));

            if (!preg_match('/X-SESSIONID=([^;]+)/', $cookieHeader, $m)) {
                throw new \RuntimeException('X-SESSIONID cookie missing');
            }

            return $m[1];
        });
    }

    /**
     * طلب عام لـ GetMetadata.
     */
    protected function requestMetadata(array $query): string
    {
        return Http::withDigestAuth($this->username, $this->password)
            ->withHeaders([
                'Accept' => 'text/xml',
                'Cookie' => "X-SESSIONID={$this->sessionId}",
            ])
            ->get("{$this->baseUrl}/Metadata.svc/GetMetadata", $query)
            ->throw()      // سيرمى استثناء إذا لم ينجح
            ->body();
    }

    /** جلب الأنواع الأساسية */
    public function getAllMetadataTypes(): array
    {
        $types = [
            'METADATA-SYSTEM',
            'METADATA-RESOURCE',
            'METADATA-CLASS',
            'METADATA-LOOKUP',
            'METADATA-LOOKUP_TYPE',
        ];

        $out = [];
        foreach ($types as $type) {
            $out[$type] = $this->requestMetadata([
                'Type'   => $type,
                'ID'     => '*',
                'Format' => 'STANDARD-XML',
            ]);
        }
        return $out;
    }

    /** جلب بيانات وصفية لمورد (Property مثلاً) */
    public function getResourceMetadata(string $resource): array
    {
        $queries = [
            'METADATA-CLASS'        => ['ID' => $resource],
            'METADATA-LOOKUP'       => ['ID' => $resource],
            'METADATA-LOOKUP_TYPE'  => ['ID' => "{$resource}:*"],  // كل اللُّك-أبس للمورد
            'METADATA-TABLE'        => ['ID' => "{$resource}:{$resource}"],
        ];

        $out = [];
        foreach ($queries as $type => $extra) {
            $out[$type] = $this->requestMetadata(array_merge([
                'Type'   => $type,
                'Format' => 'STANDARD-XML',
            ], $extra));
        }
        return $out;
    }
}

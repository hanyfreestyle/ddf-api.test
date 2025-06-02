<?php

use App\Services\DdfApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-http', function () {
    $res = Http::get('https://jsonplaceholder.typicode.com/posts/1');
    return $res->json();
});

Route::get('/ddf-test', function (DdfApiService $ddf) {
    $data = $ddf->getOpenHouses();
    return response()->json($data);
});

Route::get('/ddf/property-types', function () {
    $loginUrl = 'https://data.crea.ca/Login.svc/Login';
    $metadataUrl = 'https://data.crea.ca/Metadata.svc/GetMetadata?Type=METADATA-LOOKUP_TYPE&Format=STANDARD-XML&ID=Property:PropertyType';
    $metadataUrl = 'https://data.crea.ca/Metadata.svc/GetMetadata?Type=METADATA-LOOKUP_TYPE&Format=STANDARD-XML&ID=Property:OwnershipType';

    $username = 'CXLHfDVrziCfvwgCuL8nUahC';
    $password = 'mFqMsCSPdnb5WO1gpEEtDCHH';

    // Step 1: Login and get session cookie
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyDDFClient/1.0'
    ]);

    $loginResponse = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200 || !preg_match('/X-SESSIONID=([^;]+)/', $loginResponse, $matches)) {
        return response()->json([
            'error' => 'فشل تسجيل الدخول إلى DDF API',
            'status' => $status,
            'body' => $loginResponse,
        ], 500);
    }

    $sessionId = $matches[1];

    // Step 2: Call Metadata with session ID
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $metadataUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyDDFClient/1.0',
        "Cookie: X-SESSIONID=$sessionId"
    ]);

    $metadataResponse = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) {
        return response()->json([
            'error' => 'فشل جلب Metadata',
            'status' => $status,
            'body' => $metadataResponse,
        ], 500);
    }

    $xml = simplexml_load_string($metadataResponse);

    $types = [];
    foreach ($xml->METADATA->{'METADATA-LOOKUP_TYPE'}->LookupType as $item) {
        $types[] = [
            'id' => (string) $item->MetadataEntryID,
            'value' => (string) $item->Value,
            'name' => (string) $item->LongValue,
            'short' => (string) $item->ShortValue,
        ];
    }

    return response()->json($types);

});


Route::get('/ddf222222/sample-listings', function () {
    $username = 'CXLHfDVrziCfvwgCuL8nUahC';
    $password = 'mFqMsCSPdnb5WO1gpEEtDCHH';

    // Step 1: Login
    $loginUrl = 'https://data.crea.ca/Login.svc/Login';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyDDFClient/1.0'
    ]);
    $loginResponse = curl_exec($ch);
    curl_close($ch);

    preg_match('/X-SESSIONID=([^;]+)/', $loginResponse, $matches);
    $sessionId = $matches[1] ?? null;

    if (!$sessionId) {
        return response()->json(['error' => 'فشل في تسجيل الدخول']);
    }

    // Step 2: Get Listings
    $searchUrl = 'https://data.crea.ca/Search.svc/Search';
    $query = http_build_query([
        'SearchType' => 'Property',
        'Class' => 'Property',
        'QueryType' => 'DMQL2',
        'Query' => '(ID=*)',
        'Format' => 'STANDARD-XML',
        'Limit' => 10,
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $searchUrl . '?' . $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyDDFClient/1.0',
        "Cookie: X-SESSIONID=$sessionId"
    ]);
    $result = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: text/xml');
    echo $result;
    exit;
});

Route::get('/STANDARD-XML/sample-listings', function () {
    $username = 'CXLHfDVrziCfvwgCuL8nUahC';
    $password = 'mFqMsCSPdnb5WO1gpEEtDCHH';

    // Step 1: Login
    $loginUrl = 'https://data.crea.ca/Login.svc/Login';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyDDFClient/1.0'
    ]);
    $loginResponse = curl_exec($ch);
    curl_close($ch);

    preg_match('/X-SESSIONID=([^;]+)/', $loginResponse, $matches);
    $sessionId = $matches[1] ?? null;

    if (!$sessionId) {
        return response()->json(['error' => 'فشل تسجيل الدخول إلى DDF API']);
    }

    // Step 2: Fetch listings using LastUpdated filter
    $searchUrl = 'https://data.crea.ca/Search.svc/Search';
    $query = http_build_query([
        'SearchType' => 'Property',
        'Class' => 'Property',
        'QueryType' => 'DMQL2',
        'Query' => '(LastUpdated=2024-01-01T00:00:00)',
        'Format' => 'STANDARD-XML',
        'Limit' => 10,
        'Count' => 1,
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $searchUrl . '?' . $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyDDFClient/1.0',
        "Cookie: X-SESSIONID=$sessionId"
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) {
        return response()->json([
            'error' => 'فشل في جلب البيانات',
            'status' => $status,
            'body' => $response,
        ]);
    }

    header('Content-Type: text/xml');
    echo $response;
    exit;
});




Route::get('/ddf/sample-listings-compact', function () {
    $username = 'CXLHfDVrziCfvwgCuL8nUahC';
    $password = 'mFqMsCSPdnb5WO1gpEEtDCHH';

    // Step 1: Login
    $loginUrl = 'https://data.crea.ca/Login.svc/Login';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $loginUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyDDFClient/1.0'
    ]);
    $loginResponse = curl_exec($ch);
    curl_close($ch);

    preg_match('/X-SESSIONID=([^;]+)/', $loginResponse, $matches);
    $sessionId = $matches[1] ?? null;

    if (!$sessionId) {
        return response()->json(['error' => 'فشل تسجيل الدخول إلى DDF API']);
    }

    // Step 2: Search with Format = COMPACT
    $searchUrl = 'https://data.crea.ca/Search.svc/Search';
    $query = http_build_query([
        'SearchType' => 'Property',
        'Class' => 'Property',
        'QueryType' => 'DMQL2',
        'Query' => '(LastUpdated=2024-01-01T00:00:00)',
        'Format' => 'STANDARD-XML-DECODED',
        'Limit' => 10,
        'Count' => 1,
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $searchUrl . '?' . $query);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: MyDDFClient/1.0',
        "Cookie: X-SESSIONID=$sessionId"
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) {
        return response()->json([
            'error' => 'فشل في جلب البيانات',
            'status' => $status,
            'body' => $response,
        ]);
    }

    header('Content-Type: text/xml');
    echo $response;
    exit;
});






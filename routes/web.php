<?php

use App\Http\Controllers\MetadataController;
use App\Http\Controllers\PublicMetadataController;
use App\Http\Controllers\SmartMetadataController;
use App\Services\DdfApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/metadata/view', [SmartMetadataController::class, 'view']);

Route::get('/metadata/show', [PublicMetadataController::class, 'show']);
Route::get('/test/metadata/save', [MetadataController::class, 'saveAll']);
Route::get('test/metadata', [MetadataController::class, 'index']);
Route::get('test/metadata/{resource}', [MetadataController::class, 'showResource']);

Route::get('/test-http', function () {
    $res = Http::get('https://jsonplaceholder.typicode.com/posts/1');
    return $res->json();
});

Route::get('/ddf-test', function (DdfApiService $ddf) {
    $data = $ddf->getOpenHouses();
    return response()->json($data);
});

Route::get('/ddf-testId/', function (DdfApiService $ddf) {
    $api = new DdfApiService();
    $property = $api->getPropertyByKey(27062424);
    return response()->json($property);
});

Route::get('/ddf-testId2/', function (DdfApiService $ddf) {
    $api = new DdfApiService();
    $property = $api->getPropertyKeyFromListingId(4143136);
    return response()->json($property);
});

Route::get('/ddf/metadata-all', function () {
    $service = new DdfApiService();
    $results = $service->getAllMetadataExamples();

    return response()->json($results); // أو return view لو عاوز HTML
});


Route::get('/ddf/metadata-list', function () {
    $lookupTypes = [
        'PropertyType', 'TransactionType', 'ConstructionStatus', 'BuildingType', 'OwnershipType',
        'ZoningType', 'BasementType', 'ConstructionMaterial', 'CoolingType',
        'HeatingType', 'FireplaceFuel', 'Sewer', 'WaterSource', 'RoofMaterial',
        'ParkingType', 'ExteriorFinish', 'CommunityFeatures', 'Features',
        'AppliancesIncluded', 'LandscapeFeatures'
    ];

    $lookupTypes = [
        'MediaCategory',
        'AccessType',
        'Amenities',
        'AmenitiesNearby',
        'Amperage',
        'Appliances',
        'ArchitecturalStyle',
        'BasementDevelopment',
        'BasementFeatures',
        'BasementType',
        'Boards',
        'BuildingType',
        'BusinessSubType',
        'BusinessType',
        'CeilingType',
        'ClearCeilingHeight',
        'CommunicationType',
        'CommunityFeatures',
        'ConstructionMaterial',
        'ConstructionStatus',
        'ConstructionStyleAttachment',
        'ConstructionStyleOther',
        'ConstructionStyleSplitLevel',
        'CoolingType',
        'Crop',
        'CurrentUse',
        'DocumentType',
        'Easement',
        'EquipmentType',
        'ExteriorFinish',
        'FarmType',
        'Features',
        'FenceType',
        'FireProtection',
        'FireplaceFuel',
        'FireplaceType',
        'Fixture',
        'FlooringType',
        'FoundationType',
        'FrontsOn',
        'HeatingFuel',
        'HeatingType',
        'IrrigationType',
        'LandDispositionType',
        'LandscapeFeatures',
        'LeaseType',
        'LiveStockType',
        'LoadingType',
        'Machinery',
        'MaintenanceFeeType',
        'MeasureUnit',
        'OwnershipType',
        'ParkingType',
        'PaymentUnit',
        'PoolFeatures',
        'PoolType',
        'PropertyType',
        'RentalEquipmentType',
        'RightType',
        'RoadType',
        'RoofMaterial',
        'RoofStyle',
        'RoomLevel',
        'RoomType',
        'Sewer',
        'SignType',
        'SoilEvaluationType',
        'SoilType',
        'StorageType',
        'StoreFront',
        'StructureType',
        'SurfaceWater',
        'TopographyType',
        'TransactionType',
        'UffiCodes',
        'UtilityPower',
        'UtilityType',
        'UtilityWater',
        'ViewType',
        'WaterFrontType',
        'ZoningType',
    ];


    return view('ddf.metadata-list', compact('lookupTypes'));
});


// Route 2: عرض القيم الخاصة بأي Lookup
Route::get('/ddf/metadata-values/{lookup}', function ($lookup) {
    $loginUrl = 'https://data.crea.ca/Login.svc/Login';
    $metadataUrl = "https://data.crea.ca/Metadata.svc/GetMetadata?Type=METADATA-LOOKUP_TYPE&Format=STANDARD-XML&ID=Property:$lookup";

    $username = 'CXLHfDVrziCfvwgCuL8nUahC';
    $password = 'mFqMsCSPdnb5WO1gpEEtDCHH';

    // Step 1: Login
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
    if (!$sessionId) return "فشل تسجيل الدخول";

    // Step 2: Fetch Metadata
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
    curl_close($ch);

    $xml = simplexml_load_string($metadataResponse);

    if (!$xml || !isset($xml->METADATA->{'METADATA-LOOKUP_TYPE'})) {
        return response($metadataResponse)->header('Content-Type', 'text/xml');
    }

    $items = [];
    foreach ($xml->METADATA->{'METADATA-LOOKUP_TYPE'}->LookupType as $item) {
        $items[] = [
            'id' => (string)$item->MetadataEntryID,
            'value' => (string)$item->Value,
            'name' => (string)$item->LongValue,
            'short' => (string)$item->ShortValue,
        ];
    }

    return view('ddf.metadata-values', [
        'lookup' => $lookup,
        'items' => $items
    ]);
});






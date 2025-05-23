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



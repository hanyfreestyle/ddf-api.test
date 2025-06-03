<?php

namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class PublicMetadataController extends Controller {
    /**
     * قراءة ملف metadata.json من مجلد public وعرضه كمصفوفة JSON
     */
    public function show(): JsonResponse {
        $path = public_path('metadata.json');

        if (!File::exists($path)) {
            return response()->json(['error' => 'metadata.json file not found'], 404);
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON format'], 500);
        }

        return response()->json($data);
    }

}

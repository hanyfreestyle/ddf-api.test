<?php

namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;


class SmartMetadataController extends Controller
{
    protected string $filePath;

    public function __construct()
    {
        $this->filePath = public_path('metadata.json');
    }

    /**
     * API: /metadata/view?key=...
     */
    public function view(Request $request): JsonResponse
    {
        if (!File::exists($this->filePath)) {
            return response()->json(['error' => 'metadata.json file not found'], 404);
        }

        $json = File::get($this->filePath);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON format'], 500);
        }

        $keyPath = $request->query('key');

        if (!$keyPath) {
            return response()->json(array_keys($data));
        }

        $keys = explode('.', $keyPath);
        $current = $data;

        foreach ($keys as $key) {
            if (is_array($current)) {
                if (array_key_exists($key, $current)) {
                    $current = $current[$key];
                } elseif (is_numeric($key) && isset($current[(int)$key])) {
                    $current = $current[(int)$key];
                } else {
                    return response()->json(['error' => "Key not found: $keyPath"], 404);
                }
            } else {
                return response()->json(['error' => "Key not found: $keyPath"], 404);
            }
        }

        if (is_array($current)) {
            if (array_keys($current) !== range(0, count($current) - 1)) {
                return response()->json(['_keys' => array_keys($current)]);
            }
        }

        return response()->json($current);
    }

    /**
     * Blade UI: /metadata
     */
    public function ui()
    {
        return view('metadata.viewer');
    }
}

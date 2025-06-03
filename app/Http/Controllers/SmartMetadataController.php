<?php

namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;


class SmartMetadataController extends Controller {
    protected string $filePath;

    public function __construct() {
        $this->filePath = public_path('metadata.json'); // يمكنك تعديله إذا الملف مش في public
    }

    public function ui() {
        return view('metadata.viewer');
    }

    /**
     * عرض البيانات تدريجيًا حسب المفتاح المطلوب
     * مثال: /metadata/view?key=METADATA-LOOKUP_TYPE.Status
     */
    public function view(Request $request): JsonResponse {
        if (!File::exists($this->filePath)) {
            return response()->json(['error' => 'metadata.json file not found'], 404);
        }

        $json = File::get($this->filePath);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON format'], 500);
        }

        // key يمكن أن يكون مثل: METADATA-LOOKUP_TYPE.Status
        $keyPath = $request->query('key');

        if (!$keyPath) {
            // عرض المفاتيح العليا فقط
            return response()->json(array_keys($data));
        }

        $keys = explode('.', $keyPath);
        $current = $data;

        foreach ($keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return response()->json(['error' => "Key not found: $keyPath"], 404);
            }
            $current = $current[$key];
        }

        // لو النتيجة مصفوفة، نعرض مفاتيحها بدل المحتوى الكامل (لتصفح ذكي)
        if (is_array($current) && array_keys($current) !== range(0, count($current) - 1)) {
            return response()->json([
                '_keys' => array_keys($current)
            ]);
        }

        return response()->json($current);
    }

}

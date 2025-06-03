<?php

namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;


class SmartMetadataController extends Controller {
    protected string $filePath;

    public function __construct() {
        $this->filePath = public_path('metadata.json');
    }

    public function view(Request $request) {
        if (!File::exists($this->filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $data = json_decode(File::get($this->filePath), true);
        $key = $request->query('key');     // المفتاح الأساسي مثل METADATA-LOOKUP
        $subKey = $request->query('sub');  // المفتاح الفرعي مثل PropertyType

        // الخطوة 1: عرض المفاتيح الرئيسية
        if (!$key) {
            return view('metadata.viewer', [
                'level' => 1,
                'keys' => array_keys($data)
            ]);
        }

        if (!array_key_exists($key, $data)) {
            return response()->json(['error' => 'Key not found'], 404);
        }

        $section = $data[$key];

        // الخطوة 3: عرض تفاصيل مفتاح فرعي
        if ($subKey) {
            if (!array_key_exists($subKey, $section)) {
                return response()->json(['error' => 'Sub key not found'], 404);
            }

            $details = $section[$subKey];

            $records = [];

            if (isset($details['Lookup'])) {
                $records = $details['Lookup'];
            } elseif (isset($details['LookupValue'])) {
                $records = $details['LookupValue'];
            }

            return view('metadata.viewer', [
                'level' => 3,
                'title' => "$key > $subKey",
                'records' => $records,
            ]);
        }

        // الخطوة 2: عرض المفاتيح الفرعية داخل المفتاح الأساسي
        $subKeys = array_keys($section);

        // استبعاد المفاتيح غير المفيدة
        $excluded = ['METADATA', 'COLUMNS', 'DELIMITER'];
        $filteredSubKeys = array_filter($subKeys, fn($k) => !in_array($k, $excluded));

        return view('metadata.viewer', [
            'level' => 2,
            'title' => $key,
            'keys' => $filteredSubKeys,
            'parentKey' => $key
        ]);
    }
}

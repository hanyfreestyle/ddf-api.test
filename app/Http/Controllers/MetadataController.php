<?php

namespace App\Http\Controllers;

use App\Services\DdfService;
use App\Traits\ParsesRetsXml;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class MetadataController extends Controller {
    protected $ddfService;
    use ParsesRetsXml;


    public function __construct(private DdfService $ddf) { }


    public function saveAll(){
        // نفس الخطوات السابقة لتحويل XML⇢Array
        $data = collect($this->ddf->getAllMetadataTypes())
            ->map(fn($xml) => $this->xmlToArray($xml))
            ->all();

//        $data = [];
        // اسم ملف يضم الطابع الزمنى
        $file = 'ddf/metadata_' . now()->format('Ymd_His') . '.json';

        // خزِّنه بتنسيق مقروء
        Storage::disk('local')->put(
            $file,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        // أعِدُّه للتحميل ثم امسحه بعد الإرسال (اختياري)
        return response()
            ->download(storage_path("app/{$file}"))
            ->deleteFileAfterSend();
    }


// جلب جميع أنواع البيانات الوصفية
public
function index(): JsonResponse {
    $raw = $this->ddf->getAllMetadataTypes();

    // حوِّل كل XML إلى Array مقروء
    $parsed = collect($raw)->map(fn($xml) => $this->xmlToArray($xml));

    return response()->json($parsed);
}

// جلب بيانات وصفية لمورد معين (مثال: Property)
public
function showResource($resource) {
    $metadata = $this->ddfService->getResourceMetadata($resource);
    return response()->json($metadata);
}
}

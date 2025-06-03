<?php

namespace App\Http\Controllers;

use App\Services\DdfService;

class MetadataController extends Controller {
    protected $ddfService;

    public function __construct(DdfService $ddfService) {
        $this->ddfService = $ddfService;
    }

    // جلب جميع أنواع البيانات الوصفية
    public function index() {
        $metadata = $this->ddfService->getAllMetadataTypes();
        return response()->json($metadata);
    }

    // جلب بيانات وصفية لمورد معين (مثال: Property)
    public function showResource($resource) {
        $metadata = $this->ddfService->getResourceMetadata($resource);
        return response()->json($metadata);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DaftarAfiliasiService;
use Illuminate\Http\Request;

class DaftarAfiliasiController extends Controller
{
    protected $daftarAfiliasiService;

    public function __construct(DaftarAfiliasiService $daftarAfiliasiService)
    {
        $this->daftarAfiliasiService = $daftarAfiliasiService;
    }

    public function index(Request $request)
    {
        return $this->daftarAfiliasiService->getAllDaftarAfiliasi($request);
    }

    public function getPaginate(Request $request)
    {
        return $this->daftarAfiliasiService->getPaginateDaftarAfiliasi($request);
    }

    // public function getDaftarAfiliasi(Request $request)
    // {
    //     return $this->daftarAfiliasiService->getDaftarAfiliasi($request);
    // }
}
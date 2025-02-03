<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DaftarJurnalService;
use Illuminate\Http\Request;

class DaftarJurnalController extends Controller
{
    protected $daftarJurnalService;

    public function __construct(DaftarJurnalService $daftarJurnalService)
    {
        $this->daftarJurnalService = $daftarJurnalService;
    }

    public function index()
    {
        return $this->daftarJurnalService->getDaftarAuthor();
    }

    public function getPaginate()
    {
        return $this->daftarJurnalService->getPaginateDaftarJurnal();
    }

    public function store(Request $request)
    {
        return $this->daftarJurnalService->createJournal($request);
    }

    public function update(Request $request)
    {
        return $this->daftarJurnalService->updateJournal($request, $request->id);
    }

    public function destroy(Request $request)
    {
        return $this->daftarJurnalService->deleteJournal($request->id);
    }

    // public function getDaftarJurnal(Request $request)
    // {
    //     return $this->daftarJurnalService->getDaftarJurnal($request);
    // }
}
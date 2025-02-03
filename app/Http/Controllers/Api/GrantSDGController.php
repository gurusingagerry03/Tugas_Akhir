<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GrantSDGService;
use Illuminate\Http\Request;

class GrantSDGController extends Controller
{
    protected $grantSDGService;

    public function __construct(GrantSDGService $grantSDGService)
    {
        $this->grantSDGService = $grantSDGService;
    }

    public function index()
    {
        return $this->grantSDGService->getAllGrantSDG();
    }

    public function getPaginate()
    {
        return $this->grantSDGService->getPaginateGrantSDG();
    }

    public function show($id)
    {
        return $this->grantSDGService->getGrantSDGById($id);
    }

    public function store(Request $request)
    {
        return $this->grantSDGService->createGrantSDG($request);
    }

    public function update(Request $request, $grantId)
    {
        return $this->grantSDGService->updateGrantSDG($request, $grantId);
    }

    public function destroy($grantId)
    {
        return $this->grantSDGService->deleteGrantSDG($grantId);
    }

    public function getTotalSdgs()
    {
        return $this->grantSDGService->getTotalSdgs();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GrantFundsEksternalService;
use Illuminate\Http\Request;

class GrantFundsEksternalController extends Controller
{
    protected $grantFundsEksternalService;

    public function __construct(GrantFundsEksternalService $grantFundsEksternalService)
    {
        $this->grantFundsEksternalService = $grantFundsEksternalService;
    }

    public function index()
    {
        return $this->grantFundsEksternalService->getAllGrantFundsEksternal();
    }

    public function getPaginate()
    {
        return $this->grantFundsEksternalService->getPaginateGrantFundsEksternal();
    }

    public function show($id)
    {
        return $this->grantFundsEksternalService->getGrantFundsEksternalById($id);
    }

    public function store(Request $request)
    {
        return $this->grantFundsEksternalService->createGrantFundsEksternal($request);
    }

    public function update(Request $request, $id)
    {
        return $this->grantFundsEksternalService->updateGrantFundsEksternal($request, $id);
    }

    public function destroy($id)
    {
        return $this->grantFundsEksternalService->deleteGrantFundsEksternal($id);
    }
}

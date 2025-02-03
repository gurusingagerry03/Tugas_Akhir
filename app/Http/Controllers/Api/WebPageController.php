<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WebPageService;
use Illuminate\Http\Request;

class WebPageController extends Controller
{
    protected $webPageService;

    public function __construct(WebPageService $webPageService)
    {
        $this->webPageService = $webPageService;
    }

    public function index()
    {
        return $this->webPageService->getAllWebPage();
    }

    public function show($id)
    {
        return $this->webPageService->getWebPageById($id);
    }

    public function store(Request $request)
    {
        return $this->webPageService->createWebPage($request);
    }

    public function update(Request $request, $id)
    {
        return $this->webPageService->updateWebPage($request, $id);
    }

    public function destroy($id)
    {
        return $this->webPageService->deleteWebPage($id);
    }
}
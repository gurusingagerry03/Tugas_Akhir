<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ViewerPageService;
use Illuminate\Http\Request;

class ViewerPageController extends Controller
{

    protected $viewerPageService;

    public function __construct(ViewerPageService $viewerPageService){
        $this->viewerPageService = $viewerPageService;
    }

    public function index(Request $request)
    {
        return $this->viewerPageService->readViewerPage($request);
    }

    public function store(Request $request)
    {
        return $this->viewerPageService->createViewerPage($request);
    }

    public function update(Request $request, $id)
    {
        return $this->viewerPageService->UpdateViewerPage($request, $id);
    }

    public function destroy($id)
    {
        return $this->viewerPageService->DeleteViewerPage($id);
    }

    public function getTotalViewerByPage($page_id = null)
    {
        return $this->viewerPageService->getTotalViewerByPage($page_id);
    }
}

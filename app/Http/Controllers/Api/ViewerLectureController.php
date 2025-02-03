<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ViewerLectureService;
use Illuminate\Http\Request;

class ViewerLectureController extends Controller
{

    protected $viewerLectureService;

    public function __construct(ViewerLectureService $viewerLectureService){
        $this->viewerLectureService = $viewerLectureService;
    }

    public function index(Request $request)
    {
        return $this->viewerLectureService->getAllViewerService($request);
    }


    public function store(Request $request)
    {
        return $this->viewerLectureService->createViewerLecture($request);
    }

    public function update(Request $request, $id)
    {
        return $this->viewerLectureService->updateViewerLecture($request, $id);
    }


    public function destroy($id)
    {
        return $this->viewerLectureService->deleteViewerLecture($id);
    }
    public function getTotalViewerByAuthor($author_id = null)
    {
        return $this->viewerLectureService->getTotalViewerByAuthor($author_id);
    }
}

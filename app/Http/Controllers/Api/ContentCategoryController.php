<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContentCategoryService;
use Illuminate\Http\Request;

class ContentCategoryController extends Controller
{
    protected $contentCategoryService;

    public function __construct(ContentCategoryService $contentCategoryService)
    {
        $this->contentCategoryService = $contentCategoryService;
    }

    public function index()
    {
        return $this->contentCategoryService->getPaginateContentCategories();
    }
    
    public function getPaginate()
    {
        return $this->contentCategoryService->getPaginateContentCategories();
    }

    public function show($id)
    {
        return $this->contentCategoryService->showContentCategories($id);
    }

    public function store(Request $request)
    {
        return $this->contentCategoryService->createContentCategory($request);
    }

    public function update(Request $request, $id)
    {
        return $this->contentCategoryService->updateContentCategory($request, $id);
    }

    public function destroy($id)
    {
        return $this->contentCategoryService->deleteContentCategory($id);
    }
}
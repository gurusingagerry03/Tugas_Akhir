<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocCommunityserviceAuthorService;
use Illuminate\Http\Request;

class DocCommunityserviceAuthorController extends Controller
{
    protected $docCommunityserviceAuthorService;

    public function __construct(DocCommunityserviceAuthorService $docCommunityserviceAuthorService)
    {
        $this->docCommunityserviceAuthorService = $docCommunityserviceAuthorService;
    }

    public function index(Request $request)
    {
        return $this->docCommunityserviceAuthorService->getPaginateDocCommunityserviceAuthor($request);
    }

    public function show($id)
    {
        return $this->docCommunityserviceAuthorService->getDocCommunityserviceAuthorById($id);
    }

    public function getByAuthorId($authorId)
    {
        return $this->docCommunityserviceAuthorService->getDocCommunityserviceAuthorByAuthorId($authorId);
    }

    public function store(Request $request)
    {
        return $this->docCommunityserviceAuthorService->createDocCommunityserviceAuthor($request);
    }

    public function update(Request $request, $id)
    {
        return $this->docCommunityserviceAuthorService->updateDocCommunityserviceAuthor($request, $id);
    }

    public function destroy($id)
    {
        return $this->docCommunityserviceAuthorService->deleteDocCommunityserviceAuthor($id);
    }

    public function sync()
    {
        return $this->docCommunityserviceAuthorService->syncFromSinta();
    }
    
    public function exportDataExcel($implementationYear)
    {
        return $this->docCommunityserviceAuthorService->exportDataDocCommunityServiceAuthor($implementationYear);
    }

    public function importDataExcel(Request $request)
    {
        return $this->docCommunityserviceAuthorService->importDataDocCommunityServiceAuthor($request);
    }
}

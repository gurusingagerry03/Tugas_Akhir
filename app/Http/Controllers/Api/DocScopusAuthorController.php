<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocScopusAuthorService;
use Illuminate\Http\Request;

class DocScopusAuthorController extends Controller
{
    protected $docScopusAuthorService;

    public function __construct(DocScopusAuthorService $docScopusAuthorService)
    {
        $this->docScopusAuthorService = $docScopusAuthorService;
    }

    public function index(Request $request)
    {
        return $this->docScopusAuthorService->getPaginateDocScopusAuthor($request);
    }

    public function show($id)
    {
        return $this->docScopusAuthorService->getDocScopusAuthorById($id);
    }

    public function getByAuthorId($authorId)
    {
        return $this->docScopusAuthorService->getByAuthorId($authorId);
    }

    public function store(Request $request)
    {
        return $this->docScopusAuthorService->createDocScopusAuthor( $request);
    }

    public function update(Request $request, $id)  
    {
        return $this->docScopusAuthorService->updateDocScopusAuthor($request, $id);
    }

    public function destroy($id)
    {
        return $this->docScopusAuthorService->deleteDocScopusAuthor($id);
    }

    public function sync(Request $request)
    {
        return $this->docScopusAuthorService->syncFromSinta($request);
    }
    
}

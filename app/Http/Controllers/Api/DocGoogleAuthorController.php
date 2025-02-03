<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocGoogleAuthorService;
use App\Services\GoogleDocService;
use Illuminate\Http\Request;

class DocGoogleAuthorController extends Controller
{

    protected $docGoogleAuthorService;

    public function __construct(DocGoogleAuthorService $docGoogleAuthorService)
    {
        $this->docGoogleAuthorService = $docGoogleAuthorService;
    }

    public function index(Request $request)
    {
        return $this->docGoogleAuthorService->getPaginateDocGoogleAuthor($request);
    }

    public function show($id)
    {
        return $this->docGoogleAuthorService->getDocGoogleAuthorById($id);
    }

    public function showByAuthorId($authorId)
    {
        return $this->docGoogleAuthorService->getDocGoogleAuthorByAuthorId($authorId);
    }

    public function store(Request $request)
    {
        return $this->docGoogleAuthorService->createDocGoogleAuthor($request);
    }

    public function update(Request $request, $id)
    {
        return $this->docGoogleAuthorService->updateDocGoogleAuthor($request, $id);
    }

    public function delete($id)
    {
        return $this->docGoogleAuthorService->deleteDocGoogleAuthor($id);
    }

    public function syncFromSinta()
    {
        return $this->docGoogleAuthorService->syncFromSintaDocGoogleAuthor();
    }
}

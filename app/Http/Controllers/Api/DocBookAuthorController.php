<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocBookAuthorService;
use Illuminate\Http\Request;

class DocBookAuthorController extends Controller
{
    protected $docBookAuthorService;

    public function __construct(DocBookAuthorService $docBookAuthorService)
    {
        $this->docBookAuthorService = $docBookAuthorService;
    }

    public function index(Request $request)
    {
        return $this->docBookAuthorService->getPaginateBookDocService($request);
    }

    public function show($id)
    {
        return $this->docBookAuthorService->getDocBookAuthorById($id);
    }

    public function showByAuthorId($authorId)
    {
        return $this->docBookAuthorService->getDocBookAuthorByAuthorId($authorId);
    }

    public function store(Request $request)
    {
        return $this->docBookAuthorService->createBookDocService($request);
    }

    public function update(Request $request, $id)
    {
        return $this->docBookAuthorService->updateBookDocService($request, $id);
    }

    public function destroy($id)
    {
        return $this->docBookAuthorService->deleteBookDocService($id);
    }

    public function syncFromSinta()
    {
        return $this->docBookAuthorService->syncFromSintaDocBookAuthor();
    }

}

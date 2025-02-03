<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocWosAuthorService;
use Illuminate\Http\Request;

class DocWosAuthorController extends Controller
{
    protected $wosDocService;

    public function __construct(DocWosAuthorService $wosDocService)
    {
        $this->wosDocService = $wosDocService;
    }

    public function index(Request $request)
    {
        return $this->wosDocService->getPaginateDocWosAuthor($request);
    }

    public function getDocWosAuthorById($id)
    {
        return $this->wosDocService->getDocWosAuthorById($id);
    }

    public function getDocWosAuthorByAuthorId($authorId)
    {
        return $this->wosDocService->getDocWosAuthorByAuthorId($authorId);
    }

    public function store(Request $request)
    {
        return $this->wosDocService->createDocWosAuthor($request);
    }

    public function update(Request $request, $id)
    {
        return $this->wosDocService->updateDocWosAuthor($request, $id);
    }

    public function destroy(Request $request)
    {
        return $this->wosDocService->deleteDocWosAuthor($request->id);
    }

    public function syncFromSinta()
    {
        return $this->wosDocService->syncFromSintaDocWosAuthor();
    }

}

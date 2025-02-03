<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocGarudaAuthorService;
use Illuminate\Http\Request;

class DocGarudaAuthorController extends Controller
{
    protected $DocGarudaAuthorService;

    public function __construct(DocGarudaAuthorService $DocGarudaAuthorService)
    {
        $this->DocGarudaAuthorService = $DocGarudaAuthorService;
    }

    public function index(Request $request)
    {
        return $this->DocGarudaAuthorService->getPaginatedGarudaDoc($request);
    }

    public function show($id)
    {
        return $this->DocGarudaAuthorService->getDocGarudaAuthorById($id);
    }

    public function showByAuthorId($authorId)
    {
        return $this->DocGarudaAuthorService->getdocGarudaAuthorByAuthorId($authorId);
    }

    public function store(Request $request)
    {
        return $this->DocGarudaAuthorService->createGarudaDoc($request);
    }

    public function update(Request $request, $id)
    {
        return $this->DocGarudaAuthorService->updateGarudaDoc($request, $id);
    }

    public function destroy($id)
    {
        return $this->DocGarudaAuthorService->deleteGarudaDoc($id);
    }

    public function syncFromSinta(){
        return $this->DocGarudaAuthorService->syncFromSintaDocGarudaAuthor();
    }
}
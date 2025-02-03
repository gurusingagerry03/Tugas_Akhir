<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocResearchAuthorService;
use Illuminate\Http\Request;

class DocResearchAuthorController extends Controller
{
    protected $docResearchAuthor;

    public function __construct(DocResearchAuthorService $docResearchAuthor)
    {
        $this->docResearchAuthor = $docResearchAuthor;
    }

    public function index(Request $request)
    {
        return $this->docResearchAuthor->getPaginationDocResearchAuthor($request);
    }

    public function getDocResearchAuthorById($id)
    {
        return $this->docResearchAuthor->getDocResearchAuthorById($id);
    }

    public function getDocResearchAuthorByAuthorId($authorId)
    {
        return $this->docResearchAuthor->getDocResearchAuthorByAuthorId($authorId);
    }

    public function update(Request $request, $id)
    {
        return $this->docResearchAuthor->updateDocResearchAuthor($request, $id);
    }

    public function destroy(Request $request)
    {
        return $this->docResearchAuthor->deleteDocResearchAuthor($request->id);
    }

    public function store(Request $request)
    {
        return $this->docResearchAuthor->insertDocResearchAuthor($request);
    }

    public function syncFromSinta()
    {
        return $this->docResearchAuthor->syncFromSintaDocResearchAuthor();
    }

    public function exportDataExcel($implementationYear)
    {
        return $this->docResearchAuthor->exportDataDocResearchAuthor($implementationYear);
    }

    public function importDataExcel(Request $request)
    {
        return $this->docResearchAuthor->importDataDocResearchAuthor($request);
    }
}

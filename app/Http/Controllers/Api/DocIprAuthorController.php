<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocIprAuthorService;
use Illuminate\Http\Request;

class DocIprAuthorController extends Controller
{
    protected $docIprAuthor;

    public function __construct(DocIprAuthorService $docIprAuthor)
    {
        $this->docIprAuthor = $docIprAuthor;
    }

    public function index(Request $request)
    {
        return $this->docIprAuthor->getPaginateDocIprAuthor($request);
    }

    public function show($id)
    {
        return $this->docIprAuthor->getDocIprAuthorById($id);
    }

    public function showByAuthorId($authorId)
    {
        return $this->docIprAuthor->getDocIprAuthorByAuthorId($authorId);
    }

    public function store(Request $request)
    {
        return $this->docIprAuthor->createDocIprAuthor($request);
    }

    public function update(Request $request, $id)
    {
        return $this->docIprAuthor->updateDocIprAuthor($request, $id);
    }

    public function destroy($id)
    {
        return $this->docIprAuthor->deleteDocIprAuthor($id);
    }

    public function syncFromSinta()
    {
        return $this->docIprAuthor->syncFromSintaDocIprAuthor();
    }
}

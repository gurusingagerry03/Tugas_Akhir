<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        return $this->articleService->getPaginationArticle($request);
    }

    public function getArticleAdmin(Request $request)
    {
        return $this->articleService->getArticleAdmin($request);
    }

    public function getPaginate(Request $request)
    {
        return $this->articleService->getPaginationArticle($request);
    }

    public function show($id)
    {
        return $this->articleService->getArticleById($id);
    }

    public function store(Request $request)
    {
        return $this->articleService->createArticle($request);
    }

    public function update(Request $request, $id)
    {
        return $this->articleService->updateArticle($request, $id);
    }

    public function destroy($id)
    {
        return $this->articleService->deleteArticle($id);
    }
}

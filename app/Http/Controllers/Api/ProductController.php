<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        return $this->productService->GetPaginationProduct($request);
    } 

    public function show($id)
    {
        return $this->productService->getProductById($id);
    }

    public function getProductByAuthorId($authorId)
    {
        return $this->productService->getProductByAuthorId($authorId);
    }

    public function update(Request $request, $id)
    {
        return $this->productService->updateProduct($request, $id);
    }

    public function destroy($id)
    {
        return $this->productService->deleteProduct($id);
    }

    public function store(Request $request)
    {
        return $this->productService->insertProduct($request);
    }
}

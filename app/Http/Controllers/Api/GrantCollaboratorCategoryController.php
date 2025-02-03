<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GrantCollaboratorCategoryService;
use Illuminate\Http\Request;

class GrantCollaboratorCategoryController extends Controller
{
    protected $grantCollaboratorCategoryService;

    public function __construct(GrantCollaboratorCategoryService $grantCollaboratorCategoryService)
    {
        $this->grantCollaboratorCategoryService = $grantCollaboratorCategoryService;
    }

    public function index()
    {
        return $this->grantCollaboratorCategoryService->getAllGrantCollaboratorCategory();
    }

    public function getPaginate()
    {
        return $this->grantCollaboratorCategoryService->getPaginateGrantCollaboratorCategory();
    }

    public function show($id)
    {
        return $this->grantCollaboratorCategoryService->getGrantCollaboratorCategoryById($id);
    }

    public function store(Request $request)
    {
        return $this->grantCollaboratorCategoryService->createGrantCollaboratorCategory($request);
    }

    public function update(Request $request, $id)
    {
        return $this->grantCollaboratorCategoryService->updateGrantCollaboratorCategory($request, $id);
    }

    public function destroy($id)
    {
        return $this->grantCollaboratorCategoryService->deleteGrantCollaboratorCategory($id);
    }
}

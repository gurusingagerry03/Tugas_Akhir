<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProfileAuthorService;
use Illuminate\Http\Request;

class ProfileAuthorController extends Controller
{
    protected $ProfileAuthorService;

    public function __construct(ProfileAuthorService $ProfileAuthorService)
    {
        $this->ProfileAuthorService = $ProfileAuthorService;
    }

    public function index(Request $request)
    {
        return $this->ProfileAuthorService->getPaginatedProfileAuthor($request);
    }

    public function show($id)
    {
        return $this->ProfileAuthorService->getProfileAuthorById($id);
    }

    public function store(Request $request)
    {
        return $this->ProfileAuthorService->createProfileAuthor($request);
    }

    public function update(Request $request, $id)
    {
        return $this->ProfileAuthorService->updateAuthor($request, $id);
    }

    public function destroy($id)
    {
        return $this->ProfileAuthorService->deleteAuthor($id);
    }

    public function syncFromSinta(){
        return $this->ProfileAuthorService->syncFromSinta();
    }
}
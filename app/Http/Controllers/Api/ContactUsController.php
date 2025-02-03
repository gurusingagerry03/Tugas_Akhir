<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ContactUsService;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    protected $contactUsService;

    public function __construct(ContactUsService $contactUsService)
    {
        $this->contactUsService = $contactUsService;
    }

    public function index(Request $request)
    {
        return $this->contactUsService->getPaginateContactUs($request);
    }

    public function show($id)
    {
        return $this->contactUsService->getByIdContactUs($id);
    }

    public function store(Request $request)
    {
        return $this->contactUsService->createContactUs($request);
    }

    public function update(Request $request, $id)
    {
        return $this->contactUsService->updateContactUs($request, $id);
    }

    public function destroy($id)
    {
        return $this->contactUsService->deleteContactUs($id);
    }
}

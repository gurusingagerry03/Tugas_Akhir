<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProfileProgramService;
use Illuminate\Http\Request;

class ProfileProgramController extends Controller
{
    protected $profileProgramService;
    public function __construct(ProfileProgramService $profileProgramService){
        $this->profileProgramService = $profileProgramService;
    }

    public function index()
    {
        return $this->profileProgramService->getAllProfileProgram();;
    }

    public function getPaginate()
    {
        return $this->profileProgramService->getPaginationProfileProgram();
    }
 
    public function show($id)
    {
        return $this->profileProgramService->getProfileProgramById($id);
    }

    public function update(Request $request, $id){
        return $this->profileProgramService->updateProfileProgram($request, $id);
    }

    public function destroy($id)
    {
        return $this->profileProgramService->deleteProfileProgram($id);
    }

    public function store(Request $request){
        return $this->profileProgramService->createProfileProgram($request);
    }

    public function syncFromSinta(){
        return $this->profileProgramService->syncFromSintaProfileProgram();
    }
}

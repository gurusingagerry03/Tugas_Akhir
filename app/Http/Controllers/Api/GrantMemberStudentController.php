<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GrantMemberStudentService;
use Illuminate\Http\Request;

class GrantMemberStudentController extends Controller
{
    protected $grantMemberStudent;

    public function __construct(GrantMemberStudentService $grantMemberStudent)
    {
        $this->grantMemberStudent = $grantMemberStudent;
    }

    public function index(Request $request)
    {
        return $this->grantMemberStudent->GetPaginationGrantMemberStudent($request);
    }

    public function show($id)
    {
        return $this->grantMemberStudent->getGrantMemberStudentById($id);
    }

    public function store(Request $request)
    {
        return $this->grantMemberStudent->insertGrantMemberStudent($request);
    }

    public function update(Request $request, $id)
    {
        return $this->grantMemberStudent->updateGrantMemberStudent($request, $id);
    }


    public function destroy($id)
    {
        return $this->grantMemberStudent->deleteGrantMemberStudent($id);
    }
}

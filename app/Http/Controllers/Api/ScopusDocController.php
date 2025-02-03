<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ScopusDocService;
use Illuminate\Http\Request;

class ScopusDocController extends Controller
{
    protected $scopusDoc;
    public function __construct(ScopusDocService $scopusDoc){
        $this->scopusDoc = $scopusDoc;
    }
    //Read All Product
    public function index(Request $request)
    {
        return $this->scopusDoc->getAllScopusDoc($request);
    }

    public function getPaginate()
    {
        return $this->scopusDoc->getPaginatescopusDoc();
    }

}

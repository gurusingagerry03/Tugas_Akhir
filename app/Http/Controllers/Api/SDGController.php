<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SDGService;
use Illuminate\Http\Request;

class SDGController extends Controller
{
    protected $SDG;

    public function __construct(SDGService $SDG)
    {
        $this->SDG = $SDG;
    }
    
    public function index()
    {
        return $this->SDG->getAllSDG();
    }

    public function getPaginate()
    {
        return $this->SDG->getPaginateSDG();
    }

    public function show($id)
    {
        return $this->SDG->getSDGById($id);
    }
    
    public function store(Request $request)
    {
        return $this->SDG->createSDG($request);
    }
    
    public function update(Request $request, $id)
    {
        return $this->SDG->updateSDG($request, $id);
    }
    
    public function destroy($id)
    {
        return $this->SDG->deleteSDG($id);
    }
}

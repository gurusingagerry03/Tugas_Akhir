<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\ProfileFaculty;

class ProfileFacultyService
{
    public static function getAllFaculty()
    {
        try {
            $data = ProfileFaculty::orderBy('id', 'asc')->get();

            return response()->json([
                'status'=> true,
                'message'=>'Faculty retrieved successfully',
                'data' => $data
            ], 200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Failed to get all faculty',
                'error' => $th->getMessage()
            ], 500);
        }

    }
}
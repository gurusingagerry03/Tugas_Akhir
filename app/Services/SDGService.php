<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\SDG;

class SDGService{

    public static function getAllSDG()
    {
        try {
            $sdg = SDG::all();

            return response()->json([
                'message' => 'SDG retrieved successfully',
                'status' => true,
                'data' => $sdg
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to retrieve SDG',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getPaginateSDG()
    {
        try {
            $sdg = SDG::paginate();

            return response()->json([
                'message' => 'SDG retrieved successfully',
                'status' => true,
                'data' => $sdg
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to retrieve SDG',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSDGById($id)
    {
        try {
            $data=SDG::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'SDG found',
                'data' => $data
            ], 200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'SDG not found',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    public static function createSDG(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
            ]);

            $validated = SDG::create($validated);

            return response()->json([
                'message' => 'SDG added successfully',
                'status' => true,
            ], 200);
        } catch (\Throwable $e){
            return response()->json([
                'message' => 'failed to add the SDG',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function updateSDG(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required',
            ]);

            $sdg = SDG::findOrFail($id);
            $sdg->update($validated);

            return response()->json([
                'message' => 'SDG updated successfully',
                'status' => true,
                'data' => $sdg
            ], 200);

        } catch (\Throwable $e){
            return response()->json([
                'message' => 'failed to update the SDG',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function deleteSDG($id)
    {
        try {
            $sdg = SDG::findOrFail($id);
            $sdg->delete();

            return response()->json([
                'message' => 'SDG deleted successfully',
                'status' => true,
            ], 200);
        } catch (\Throwable $e){
            return response()->json([
                'message' => 'failed to delete the SDG',
                'status' => false,
                'error' => $e->getMessage()
            ], 404);
        }
    }
}

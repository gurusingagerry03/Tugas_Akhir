<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\GrantSDG;
use App\Models\SDG;
use App\Rules\GrantIdExists;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\MemberResearch;
use App\Models\MemberCommunityservice;

class GrantSDGService{

    public static function getAllGrantSDG()
    {
        try {
            $data = GrantSDG::with('grant', 'sdg')->get();

            return response()->json([
                'message' => 'grant sdg retrieved successfully',
                'status' => true,
                'data' => $data
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to retrieve grant sdg',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getPaginateGrantSDG()
    {
        try {
            $data = GrantSDG::with('sdg')->paginate();

            return response()->json([
                'message' => 'grant sdg retrieved successfully',
                'status' => true,
                'data' => $data
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to retrieve grant sdg',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getGrantSDGById(string $id)
    {
        try{
            $data = GrantSDG::with('sdg')->findOrFail($id);

            return response()->json([
                'message' => 'grant sdg retrieved successfully',
                'status' => true,
                'data' => $data
            ], 200);
        } catch (\Throwable $e){
            return response()->json([
                'message' => 'Failed to retrieve grant sdg',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createGrantSDG(Request $request)
    {
        try{
            $validated = $request->validate([
                'grant_id' => ['required', new GrantIdExists],
                'sdgs_id' => 'required|array',
                'sdgs_id.*' => 'exists:sdgs,id',
            ]);

            $validated['grant_category_id'] = DB::table('doc_communityservice_author')
                ->where('id', $validated['grant_id'])
                ->exists() ? 2 : 1;

            foreach ($validated['sdgs_id'] as $sdgId) {
                GrantSDG::create([
                    'grant_category_id' => $validated['grant_category_id'],
                    'grant_id' => $validated['grant_id'],
                    'sdgs_id' => $sdgId,
                ]);
            }

            return response()->json([
                'message' => 'grant sdg added successfully',
                'status' => true,
            ],200);

        } catch (ValidationException $e){
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e){
            return response()->json([
                'message' => 'failed to add the grant sdg',
                'status' => false,
                'error' => $e->getMessage()], 500);
        }
    }

    public static function updateGrantSDG(Request $request, $grantId)
    {
        try {
            $validated = $request->validate([
                'sdgs_id' => 'required|array',
                'sdgs_id.*' => 'exists:sdgs,id',
            ]);

            $validated['grant_category_id'] = DB::table('doc_communityservice_author')
                ->where('id', $grantId)
                ->exists() ? 2 : 1;

            GrantSDG::where('grant_id', $grantId)->delete();

            foreach ($validated['sdgs_id'] as $sdgId) {
                GrantSDG::create([
                    'grant_category_id' => $validated['grant_category_id'],
                    'grant_id' => $grantId,
                    'sdgs_id' => $sdgId,
                ]);
            }

            return response()->json([
                'message' => 'grant sdg updated successfully',
                'status' => true
            ], 200);

        } catch (ValidationException $e){
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e){
            return response()->json([
                'message' => 'failed to update the grant sdg',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function deleteGrantSDG($grantId)
    {
        try {
            GrantSDG::where('grant_id', $grantId)->delete();

            return response()->json([
                'message' => 'grant sdg deleted successfully',
                'status' => true,
            ], 202);
        } catch (\Throwable $e){
            return response()->json([
                'message' => 'failed to delete the grant sdg',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function getTotalSdgs()
    {
        try {
            $sdgs = SDG::with('grantSdgs')
                ->get()
                ->map(function ($sdg) {
                    $totalResearcher = MemberResearch::whereHas('docResearchAuthor', function ($query) use ($sdg) {
                        $query->whereHas('grantSdgs', function ($subQuery) use ($sdg) {
                            $subQuery->where('sdgs_id', $sdg->id);
                        });
                    })
                    ->distinct('name')
                    ->count();

                    $totalResearcher += MemberCommunityservice::whereHas('communityService', function ($query) use ($sdg) {
                        $query->whereHas('grantSdgs', function ($subQuery) use ($sdg) {
                            $subQuery->where('sdgs_id', $sdg->id);
                        });
                    })
                    ->distinct('name')
                    ->count();

                    $totalResearch = $sdg->grantSdgs
                        ->where('grant_category_id', 1)
                        ->count();

                    $totalCommunityService = $sdg->grantSdgs
                        ->where('grant_category_id', 2)
                        ->count();

                    return [
                        'id' => $sdg->id,
                        'name' => $sdg->name,
                        'sdg_details' => [
                            'total_researcher' => $totalResearcher,
                            'total_research' => $totalResearch,
                            'total_community_service' => $totalCommunityService,
                        ],
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => "Total Grant SDGs retrieved successfully",
                'data' => $sdgs,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve total Grant SDGs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}

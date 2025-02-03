<?php

namespace App\Services;

use App\Models\LogSyncSinta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ProfileProgram;
use Illuminate\Support\Facades\Auth;

class ProfileProgramService{

    public static function getAllProfileProgram()
    {
        try {
            $profilprogram = ProfileProgram::with('affiliation:id,code_Pddikti,name')->get();
            return response()->json([
                'status'=>true,
                'message'=>'All Profile Program found',
                'data' => $profilprogram
            ],200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'failed to get All Profile Program',
                'error' => $th->getMessage()], 500);
        }
    }

    public static function getPaginationProfileProgram()
    {
        try {

            $profilprogram = ProfileProgram::with('affiliation:id,code_Pddikti,name')->paginate();

            return response()->json(
                [
                    'message' => 'Profile Program retrieved successfully',
                    'status' => true,
                    'data' => $profilprogram
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Failed to retrieve Profile Program',
                    'status' => false,
                    'error' => $e->getMessage(),
                ], 200
            );
        }
    }

    public static function getProfileProgramById($id)
    {
        try{
            $data=ProfileProgram::with('affiliation:id,code_Pddikti,name')->findOrFail($id);
            return response()->json([
                'status'=>true,
                'message'=>'Profile Program found',
                'data'=>$data
            ],200);
        }catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Profile Program not found',
                'error' => $th->getMessage()], 500);
        }
    }

    public static function createProfileProgram(Request $request)
    {
        try {
            ProfileProgram::create([
                'id'                                    => $request->id,
                'faculty_id'                            => $request->faculty_id,
                'code_pddikti'                          => $request->code_pddikti,
                'name_id'                               => $request->name_id,
                'name_en'                               => $request->name_en,
                'website'                               => $request->website,
                'level'                                 => $request->level,
                'affiliation_id'                        => $request->affiliation_id,
                'sinta_score_v3_overall'                => $request->sinta_score_v3_overall,
                'sinta_score_v3_3year'                  => $request->sinta_score_v3_3year,
                'sinta_score_v3_productivity_overall'   => $request->sinta_score_v3_productivity_overall,
                'sinta_score_v3_productivity_3year'     => $request->sinta_score_v3_productivity_3year

            ]);

            return response()->json([
                'status' => true,
                'message' => 'Profile Program successfully created',

            ],200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'create Profile Program failed',
                'error' => $th->getMessage()], 500);
        }
    }

    public static function updateProfileProgram(Request $request, $id)
    {
        try {
            $profilprogram=ProfileProgram::findOrFail($id);
            $profilprogram->update([
                'faculty_id'                            => $request->faculty_id,
                'code_pddikti'                          => $request->code_pddikti,
                'name_id'                               => $request->name_id,
                'name_en'                               => $request->name_en,
                'website'                               => $request->website,
                'level'                                 => $request->level,
                'affiliation_id'                        => $request->affiliation_id,
                'sinta_score_v3_overall'                => $request->sinta_score_v3_overall,
                'sinta_score_v3_3year'                  => $request->sinta_score_v3_3year,
                'sinta_score_v3_productivity_overall'   => $request->sinta_score_v3_productivity_overall,
                'sinta_score_v3_productivity_3year'     => $request->sinta_score_v3_productivity_3year

            ]);
            return response()->json([
                'status'=>true,
                'message'=>'Profile Program successfully updated',
                'data' => $profilprogram
            ],200);
        } catch (\Throwable $th){
            return response()->json([
                'status' =>false,
                'message' => 'update Profile Program failed',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function deleteProfileProgram($id)
    {
        try {
            $profilprogram = ProfileProgram::findorFail($id);
            $profilprogram->delete();
            return response()->json([
                'status' => true,
                'message' => 'Profile Program deleted'
            ], 200);
        } catch (\Throwable $th){
            return response()->json([
                'status' => false,
                'message' => 'Profile Program  failed',
                'error' => $th->getMessage()], 500);
        }
    }

    public static function syncFromSintaProfileProgram()
    {
        try {
            $tokenSinta = LoginSintaService::loginSinta(new Request());
            $totalDocumentsFetched = 0;
            $processedprograms_id = [];

            $sintaApi = "http://apisinta.kemdikbud.go.id/v3/dev/271071775/programs";

            $res = Http::withToken($tokenSinta)->post($sintaApi);

            if ($res->failed()) {
                return response()->json(
                    [
                        'message' => "Failed to Sync from SINTA ",
                        'status' => false,
                        'error' => $res->body(),
                    ],
                    400
                );
            }
            $data = $res->json();
            $profilePrograms = $data['results']['programs'];
            $totalDocuments = $data['results']['total'];

            foreach ($profilePrograms as $profileProgram) {
                $programs_id = $profileProgram['programs_id'];

                if (!in_array($programs_id, $processedprograms_id)) {
                    $processedprograms_id[] = $programs_id;

                    ProfileProgram::updateOrCreate(
                        [
                            'id' => $programs_id,
                        ],
                        [

                            'code_pddikti' => $profileProgram['code_pddikti'] ?? '',
                            'faculty_id' => !empty($doc['faculty_id']) ? $doc['faculty_id'] : 0,
                            'name_id' => $profileProgram['name'] ?? '',
                            'name_en' => !empty($doc['name_en']) ? $doc['name_en'] : 0,
                            'website' => $profileProgram['website'] ?? '',
                            'level' => $profileProgram['level'] ?? '',
                            'affiliation_id' => $profileProgram['affiliation']['id'] ?? '',
                            'sinta_score_v3_overall' => $profileProgram['sinta_score_v3_overall'] ?? '',
                            'sinta_score_v3_3year' => $profileProgram['sinta_score_v3_3year'] ?? '',
                            'sinta_score_v3_productivity_overall' => $profileProgram['sinta_score_v3_productivity_overall'] ?? '',
                            'sinta_score_v3_productivity_3year' => $profileProgram['sinta_score_v3_productivity_3year'] ?? '',
                        ]
                    );
                    $totalDocumentsFetched++;
                }
            }

            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Profile Program',
                'status' => 'success',
            ]);

            return response()->json([
                "message" => "Synced program data from SINTA successfully",
                "status" => true,
            ], 200);
        } catch (\Throwable $th) {
            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Profile Program',
                'status' => 'failed',
            ]);
            
            return response()->json([
                "message" => "Failed to sync program data from SINTA",
                "status" => 'false',
                "error" => $th->getMessage(),
            ], 500);
        }
    }
}

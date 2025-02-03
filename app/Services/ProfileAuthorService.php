<?php

namespace App\Services;

use App\Models\GarudaAuthor;
use App\Models\GoogleAuthor;
use App\Models\LogSyncSinta;
use App\Models\ScopusAuthor;
use App\Models\WosAuthor;
use Illuminate\Http\Request;
use App\Models\ProfileAuthor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Storage;

class ProfileAuthorService
{
    public static function syncFromSinta()
    {
        try {
            $tokenSinta = LoginSintaService::loginSinta(new Request());
            $sintaApiUrl = "http://apisinta.kemdikbud.go.id/v3/dev/271071775/authors";
            $response = Http::withToken($tokenSinta)->post($sintaApiUrl);
            $arr = [];

            foreach ($response->json()["results"]["authors"] as $authorData) {

                if (isset($authorData['image'])) {
                    $authorData['image'] = $authorData->file('image')->store('images', 'public');
                }

                $profileAuthor = ProfileAuthor::updateOrCreate(
                    [
                        'id' => $authorData['id'],
                        'nidn' => $authorData['NIDN']
                    ],
                    [
                        'programs_id' => $authorData['programs']['code'],
                        'affiliation_id' => $authorData['affiliation']['id'],
                        'fullname' => $authorData['fullname'],
                        'country' => $authorData['country'],
                        'academic_grade_raw' => $authorData['academic_grade_raw'],
                        'academic_grade' => $authorData['academic_grade'],
                        'gelar_depan' => $authorData['gelar_depan'],
                        'gelar_belakang' => $authorData['gelar_belakang'],
                        'last_education' => $authorData['last_education'],
                        'sinta_score_v2_overall' => $authorData['sinta_score_v2_overall'],
                        'sinta_score_v2_3year' => $authorData['sinta_score_v2_3year'],
                        'sinta_score_v3_overall' => $authorData['sinta_score_v3_overall'],
                        'sinta_score_v3_3year' => $authorData['sinta_score_v3_3year'],
                        'affiliation_score_v3_overall' => $authorData['affiliation_score_v3_overall'] !== "" ? $authorData['affiliation_score_v3_overall'] : 0,
                        'affiliation_score_v3_3year' => $authorData['affiliation_score_v3_3year'] !== "" ? $authorData['affiliation_score_v3_3year'] : 0,
                        'image' => $authorData['image'] ?? "",
                    ]
                );

                if (isset($authorData['scopus'])) {
                    ScopusAuthor::updateOrCreate(
                        ['author_id' => $profileAuthor->id],
                        [
                            'total_document' => $authorData['scopus']['total_document'],
                            'total_citation' => $authorData['scopus']['total_citation'],
                            'total_cited_doc' => $authorData['scopus']['total_cited_doc'],
                            'h_index' => $authorData['scopus']['h_index'],
                            'i10_index' => $authorData['scopus']['i10_index'],
                            'g_index' => $authorData['scopus']['g_index'],
                            'g_index_3year' => $authorData['scopus']['g_index_3year'],
                        ]
                    );
                }

                if (isset($authorData['wos'])) {
                    WosAuthor::updateOrCreate(
                        ['author_id' => $profileAuthor->id],
                        [
                            'total_document' => $authorData['wos']['total_document'],
                            'total_citation' => $authorData['wos']['total_citation'],
                            'total_cited_doc' => $authorData['wos']['total_cited_doc'],
                            'h_index' => $authorData['wos']['h_index'],
                        ]
                    );
                }

                if (isset($authorData['garuda'])) {
                    GarudaAuthor::updateOrCreate(
                        ['author_id' => $profileAuthor->id],
                        [
                            'total_document' => $authorData['garuda']['total_document'],
                            'total_citation' => $authorData['garuda']['total_citation'],
                            'total_cited_doc' => $authorData['garuda']['total_cited_doc'],
                        ]
                    );
                }

                if (isset($authorData['google'])) {
                    GoogleAuthor::updateOrCreate(
                        ['author_id' => $profileAuthor->id],
                        [
                            'total_document' => $authorData['google']['total_document'],
                            'total_citation' => $authorData['google']['total_citation'],
                            'total_cited_doc' => $authorData['google']['total_cited_doc'],
                            'h_index' => $authorData['google']['h_index'],
                            'i10_index' => $authorData['google']['i10_index'],
                            'g_index' => $authorData['google']['g_index'],
                        ]
                    );
                }

                $profileAuthor->save();
                $arr[] = ProfileAuthor::with(['affiliation', 'program', 'scopusAuthors', 'wosAuthors', 'garudaAuthors', 'googleAuthors'])->find($profileAuthor->id);
            }

            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Profile Author',
                'status' => 'success'
            ]);

            return response()->json([
                "status" => true,
                "message" => "Synced author data from SINTA successfully",
                "data" => $arr
            ], 200);

        } catch (\Throwable $th) {
            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Profile Author',
                'status' => 'failed'
            ]);
            
            return response()->json([
                "status" => false,
                "message" => "Failed to sync author data from SINTA",
                "error" => $th->getMessage()
            ], 500);
        }
    }

    public static function getProfileAuthor()
    {
        try {
            $data = ProfileAuthor::with(['affiliation', 'program', 'scopusAuthors', 'wosAuthors', 'garudaAuthors', 'googleAuthors', 'docResearchAuthors'])->get();
            return response()->json([
                'status' => true,
                'message' => 'Successfully found Daftar Author',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'failed to get Daftar Author',
                'error' => $th->getMessage()
            ], 500);
        }

    }

    public static function getPaginatedProfileAuthor(Request $request)
    {
        try {
            $keyword = $request->query('keyword');
            $faculty = $request->query('faculty');
            $perPage = $request->query('per_page', 10);

            $author = ProfileAuthor::with(['affiliation', 'program', 'scopusAuthors', 'wosAuthors', 'garudaAuthors', 'googleAuthors'])
                ->when($faculty, function ($query) use ($faculty) {
                    $query->whereHas('program', function ($q) use ($faculty) {
                        $q->where('faculty_id', $faculty);
                    });
                })
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('fullname', 'like', '%' . $keyword . '%');
                })
                ->paginate($perPage);
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Profile Author retrieved successfully',
                    'data' => $author,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error',
                    'data' => $th->getMessage()
                ],
                500
            );
        }
    }

    public static function getProfileAuthorById($id)
    {
        //
        try {
            $article = ProfileAuthor::with([
                'affiliation',
                'program',
                'scopusAuthors',
                'wosAuthors',
                'garudaAuthors',
                'googleAuthors',
            ])->findOrFail($id);

            $article["contributions"] = [
                "total_publication" =>
                    ($article->scopusAuthors['total_document'] ?? 0) +
                    ($article->wosAuthors['total_document'] ?? 0) +
                    ($article->garudaAuthors['total_document'] ?? 0) +
                    ($article->googleAuthors['total_document'] ?? 0),
                "total_research" => $article->docResearchAuthors->count(),
                "total_communityservice" => $article->communityServices->count(),
                "total_iprs" => $article->iprs->count(),
                "total_book" => $article->books->count(),
                "total_product" => $article->docResearchAuthors->sum(function ($docAuthor) {
                    return $docAuthor->products->count();
                }),
                "total_publication" =>
                    $article->docScopusAuthors->count() +
                    $article->docWosAuthors->count() +
                    $article->docGarudaAuthors->count() +
                    $article->docGoogleAuthors->count()
            ];

            $article->makeHidden([
                'docResearchAuthors',
                'communityServices',
                'iprs',
                'books',
                'docScopusAuthors',
                'docWosAuthors',
                'docGarudaAuthors',
                'docGoogleAuthors'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Successfully found Daftar Author with id : ' . $id,
                'data' => $article
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'failed to get Daftar Author  with id : ' . $id,
                'error' => $th->getMessage()
            ], 500);
        }

    }

    public function createProfileAuthor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'programs_id' => 'required|integer|exists:profile_programs,id',
            'affiliation_id' => 'required|integer|exists:affiliation,id',
            'nidn' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'academic_grade_raw' => 'required|string|max:255',
            'academic_grade' => 'required|string|max:255',
            'gelar_depan' => 'nullable|string|max:255',
            'gelar_belakang' => 'nullable|string|max:255',
            'last_education' => 'required|string|max:255',
            'sinta_score_v2_overall' => 'required|numeric',
            'sinta_score_v2_3year' => 'required|numeric',
            'sinta_score_v3_overall' => 'required|numeric',
            'sinta_score_v3_3year' => 'required|numeric',
            'affiliation_score_v3_overall' => 'required|numeric',
            'affiliation_score_v3_3year' => 'required|numeric',
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        try {

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('images', 'public');
            }


            $author = ProfileAuthor::create($data);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Author created successfully',
                    'data' => $author,
                ],
                201,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error creating author',
                    'data' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function updateAuthor(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'programs_id' => 'required|integer|exists:profile_programs,id',
            'affiliation_id' => 'required|integer|exists:affiliation,id',
            'nidn' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'academic_grade_raw' => 'required|string|max:255',
            'academic_grade' => 'required|string|max:255',
            'gelar_depan' => 'nullable|string|max:255',
            'gelar_belakang' => 'nullable|string|max:255',
            'last_education' => 'required|string|max:255',
            'sinta_score_v2_overall' => 'required|numeric',
            'sinta_score_v2_3year' => 'required|numeric',
            'sinta_score_v3_overall' => 'required|numeric',
            'sinta_score_v3_3year' => 'required|numeric',
            'affiliation_score_v3_overall' => 'required|numeric',
            'affiliation_score_v3_3year' => 'required|numeric',
            'image' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        try {
            $author = ProfileAuthor::findOrFail($id);

            if ($request->hasFile('image')) {
                if ($author->image && Storage::disk('public')->exists($author->image)) {
                    Storage::disk('public')->delete($author->image);
                }
                $data['image'] = $request->file('image')->store('images', 'public');
            }

            $author->update($data);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Author updated successfully',
                    'data' => $author,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error updating author',
                    'data' => $th->getMessage()
                ],
                500
            );
        }
    }

    public function deleteAuthor($id)
    {
        try {
            $author = ProfileAuthor::findOrFail($id);

            if ($author->image) {
                Storage::disk('public')->delete($author->image);
            }

            $author->delete();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Author deleted successfully',
                    'data' => $author,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error deleting author',
                    'data' => $th->getMessage()
                ],
                500
            );
        }
    }

}

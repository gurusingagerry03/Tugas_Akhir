<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DocIprAuthor;
use App\Models\LogSyncSinta;
use App\Models\ProfileAuthor;
use Exception;
use Illuminate\Support\Facades\Auth;
class DocIprAuthorService
{
    public static function getDocIprAuthor()
    {
        try {
            $docIprAuthor = DocIprAuthor::All();
            return response()->json([
                'message' => 'Doc Ipr Author retrieved successfully',
                'status' => true,
                'data' => $docIprAuthor
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve Doc Ipr Author',
                'status' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }


    public static function getPaginateDocIprAuthor(Request $request)
    {
        try {
            $faculty = $request->query('faculty');
            $keyword = $request->query('keyword');
            $requestsYear = $request->query('request_year');
            $category = $request->query('category');
            $items = $request->query('items', 10);


            $query = DocIprAuthor::with('profileAuthor.program');

            if (!empty($faculty)) {
                $query->whereHas('profileAuthor.program', function ($q) use ($faculty) {
                    $q->where('faculty_id', $faculty);
                });
            }

            if (!empty($keyword)) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%")
                      ->orWhere('inventor', 'like', "%$keyword%")
                      ->orWhereHas('profileAuthor', function ($q) use ($keyword) {
                          $q->where('fullname', 'like', "%$keyword%");
                      });
                });
            }

            if (!empty($requestsYear)) {
                $query->where('requests_year', $requestsYear);
            }

            if (!empty($category)) {
                $query->where('category', $category);
            }

            $data = $query->paginate($items);

            return response()->json([
                'message' => 'Doc Ipr Author retrieved successfully',
                'status' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve Doc Ipr Author',
                'status' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public static function getDocIprAuthorById($id)
    {
        try {
            $docIprAuthor = DocIprAuthor::with([
                'profileAuthor' => function ($query) {
                    $query->select('nidn', 'fullname', 'gelar_depan', 'gelar_belakang', 'country', 'image', 'programs_id');
                },
                'profileAuthor.program' => function ($query) {
                    $query->select('code_pddikti', 'faculty_id', 'name_id', 'name_en');
                }
            ])
                ->find($id);

            if (!$docIprAuthor) {
                return response()->json([
                    "message" => "Doc Ipr Author not found",
                    "status" => false
                ], 404);
            }

            return response()->json([
                "message" => "Success to get Doc Ipr Author",
                "status" => true,
                "data" => $docIprAuthor
            ]);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Failed to get Doc Ipr Author",
                "status" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    public static function getDocIprAuthorByAuthorId($author_id)
    {
        try {

            $docIprAuthor = DocIprAuthor::with([
                'profileAuthor' => function ($query) {
                    $query->select('nidn', 'fullname', 'gelar_depan', 'gelar_belakang', 'country', 'image', 'programs_id');
                },
                'profileAuthor.program' => function ($query) {
                    $query->select('code_pddikti', 'faculty_id', 'name_id', 'name_en');
                }
            ])->where('author_id', $author_id)->get();

            if ($docIprAuthor->isEmpty()) {
                return response()->json([
                    "message" => "Doc Ipr Author not found",
                    "status" => false
                ], 404);
            }

            return response()->json([
                "message" => "Success to get Doc Ipr Authors by author_id",
                "status" => true,
                "data" => $docIprAuthor
            ]);
        } catch (Exception $th) {
            return response()->json([
                "message" => "Failed to get Doc Ipr Authors by author_id",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }

    public static function createDocIprAuthor(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'author_id' => 'required|string',
                'category' => 'required|string|max:255',
                'requests_year' => 'required|integer|min:1900|max:2100',
                'requests_number' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'inventor' => 'required|string|max:255',
                'patent_holder' => 'required|string|max:255',
                'publication_date' => 'required|date',
                'publication_number' => 'required|string|max:255',
                'filing_date' => 'required|date',
                'reception_date' => 'required|date',
                'registration_date' => 'required|date',
                'registration_number' => 'required|string|max:255',
            ]);

            // Membuat data Doc Ipr Author
            $docIprAuthorData = DocIprAuthor::create($validatedData);

            return response()->json([
                'message' => 'Doc Ipr Author created successfully',
                'status' => true,
                'data' => $docIprAuthorData
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->validator->errors()
            ], 422);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'Failed to create Doc Ipr Author',
                'status' => false,
                'error' => $th->getMessage()
            ]);
        }
    }


    public function updateDocIprAuthor(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'author_id' => 'sometimes|required|integer',
                'category' => 'sometimes|required|string|max:255',
                'requests_year' => 'sometimes|required|integer|min:1900|max:9999',
                'requests_number' => 'sometimes|required|string|max:255',
                'title' => 'sometimes|required|string|max:255',
                'inventor' => 'sometimes|required|string|max:255',
                'patent_holder' => 'sometimes|required|string|max:255',
                'publication_date' => 'sometimes|required|date_format:Y-m-d',
                'publication_number' => 'sometimes|required|string|max:255',
                'filing_date' => 'sometimes|required|date_format:Y-m-d',
                'reception_date' => 'sometimes|required|date_format:Y-m-d',
                'registration_date' => 'sometimes|required|date_format:Y-m-d',
                'registration_number' => 'sometimes|required|string|max:255',
            ]);


            $docIprAuthor = DocIprAuthor::findOrFail($id);

            $docIprAuthor->update($validatedData);

            $updatedData = DocIprAuthor::findOrFail($id);

            return response()->json([
                'message' => 'Doc Ipr Author updated successfully',
                'status' => true,
                'data' => $updatedData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update the Doc Ipr Author',
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public static function deleteDocIprAuthor($id)
    {
        try {
            $data = DocIprAuthor::find($id);

            if (!$data) {
                return response()->json([
                    "message" => "Doc Ipr Author not found",
                    "status" => false
                ], 404);
            }

            $data->delete();

            return response()->json([
                "message" => "Doc Ipr Author deleted successfully",
                "status" => true
            ]);
        } catch (Exception $th) {
            return response()->json([
                "message" => "Failed to delete doc ipr author",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }

    public static function syncFromSintaDocIprAuthor()
    {
        try {
            $tokenSinta = LoginSintaService::loginSinta(new Request());
            $authors = ProfileAuthor::select('NIDN')->distinct()->get();

            foreach ($authors as $author) {
                $sintaApiUrl = "http://apisinta.kemdikbud.go.id/v3/dev/271071775/author/ipr/nidn/{$author->NIDN}";

                $response = Http::withToken($tokenSinta)->post($sintaApiUrl);

                if ($response->failed()) {
                    return response()->json([
                        "message" => "Failed to fetch ipr doc data from SINTA for NIDN {$author->NIDN}",
                        "status" => false,
                        "error" => $response->body()
                    ], 400);
                }

                $data = $response->json();
                $documents = $data['results']['documents'];

                foreach ($documents as $document) {
                    $id = $document['id'];

                    DocIprAuthor::updateOrCreate(
                        [
                            'id' => $id,
                        ],
                        [
                            'author_id' => $data['results']['authors']['author_id'],
                            'category' => $document['category'] ?? '',
                            'requests_year' => $document['requests_year'] ?? 0,
                            'requests_number' => $document['requests_number'] ?? 0,
                            'title' => $document['title'] ?? '',
                            'inventor' => $document['inventor'] ?? '',
                            'patent_holder' => $document['patent_holder'] ?? '',
                            'publication_date' => $document['publication_date'] ?? '',
                            'publication_number' => $document['publication_number'] ?? '',
                            'filing_date' => $document['filing_date'] ?? '',
                            'reception_date' => $document['reception_date'] ?? '',
                            'registration_date' => $document['registration_date'] ?? '',
                            'registration_number' => $document['registration_number'] ?? ''
                        ]
                    );
                }
            }

            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Ipr Author',
                'status' => 'success',
            ]);

            return response()->json([
                "message" => "Synced doc ipr author data from SINTA successfully",
                "status" => true,
                "data" => $documents
            ], 200);
        } catch (\Throwable $th) {
            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Ipr Author',
                'status' => 'failed',
            ]);

            return response()->json([
                "message" => "Failed to sync doc ipr author data from SINTA",
                "status" => false,
                "error" => $th->getMessage(),
            ], 500);
        }
    }

    // Sync Sinta dengan banyak response
    //
    // public static function syncFromSintaDocIprAuthor()
    // {
    //     ini_set('max_execution_time', 1000);
    //     try {
    //         $loginToken = LoginSintaService::loginSinta(new Request());
    //         $authors = ProfileAuthor::select('NIDN')->distinct()->get();

    //         foreach ($authors as $author) {
    //             $totalDocumentsFetched = 0;
    //             $processedId = [];

    //             do {
    //                 $sintaApiUrl = "http://apisinta.kemdikbud.go.id/v3/dev/271071775/author/ipr/nidn/{$author->NIDN}";

    //                 $response = Http::withToken($loginToken)->post($sintaApiUrl);

    //                 if ($response->failed()) {
    //                     return response()->json([
    //                         "message" => "Failed to fetch ipr doc data from SINTA for NIDN {$author->NIDN}",
    //                         "status" => false,
    //                         "error" => $response->body()
    //                     ], 400);
    //                 }

    //                 $data = $response->json();
    //                 $documents = $data['results']['documents'];
    //                 $totalDocuments = $data['results']['total'];

    //                 foreach ($documents as $document) {
    //                     $id = $document['id'];

    //                     if (!in_array($id, $processedId)) {
    //                         $processedId[] = $id;
    //                         $totalDocumentsFetched++;

    //                         DocIprAuthor::updateOrCreate(
    //                             [
    //                                 'id' => $id,
    //                             ],
    //                             [
    //                                 'author_id' => $data['results']['authors']['author_id'],
    //                                 'category' => $document['category'] ?? '',
    //                                 'requests_year' => $document['requests_year'] ?? 0,
    //                                 'requests_number' => $document['requests_number'] ?? 0,
    //                                 'title' => $document['title'] ?? '',
    //                                 'inventor' => $document['inventor'] ?? '',
    //                                 'patent_holder' => $document['patent_holder'] ?? '',
    //                                 'publication_date' => $document['publication_date'] ?? '',
    //                                 'publication_number' => $document['publication_number'] ?? '',
    //                                 'filing_date' => $document['filing_date'] ?? '',
    //                                 'reception_date' => $document['reception_date'] ?? '',
    //                                 'registration_date' => $document['registration_date'] ?? '',
    //                                 'registration_number' => $document['registration_number'] ?? ''
    //                             ]
    //                         );
    //                     }
    //                 }

    //                 if ($totalDocumentsFetched >= $totalDocuments) {
    //                     break;
    //                 }
    //             } while (count($documents) > 0);
    //         }

    //         return response()->json([
    //             "message" => "Sync ipr doc data from SINTA for all authors completed successfully",
    //             "status" => true,
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             "message" => "Failed to sync ipr doc data from SINTA",
    //             "status" => false,
    //             "error" => $e->getMessage(),
    //         ]);
    //     }
    // }

}

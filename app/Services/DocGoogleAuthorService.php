<?php

namespace App\Services;

use App\Models\DocGoogleAuthor;
use App\Models\LogSyncSinta;
use App\Models\ProfileAuthor;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DocGoogleAuthorService
{
    public static function getDocGoogleAuthor()
    {
        try {
            $data = DocGoogleAuthor::all();
            return response()->json([
                "message" => "success to get doc google author",
                "status" => true,
                "data" => $data
            ]);
        } catch (Exception $th) {
            return response()->json([
                "message" => "Failed to get doc google author",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }

    public static function getPaginateDocGoogleAuthor(Request $request)
    {
        try {
            $keyword = $request->query('keyword');
            $year = $request->query('year');
            $items = $request->query('items', 10);

            $query = DocGoogleAuthor::with('profileAuthor.program');

            if (!empty($year)) {
                $query->where('publish_year', $year);
            }

            if (!empty($keyword)) {
                $query->when($keyword, function ($query, $keyword) {
                    return $query->where('title', 'like', "%$keyword%")
                        ->orWhere('abstract', 'like', "%$keyword%")
                        ->orWhere('authors', 'like', "%$keyword%")
                        ->orWhereHas('profileAuthor', function ($query) use ($keyword) {
                            $query->where('fullname', 'like', "%$keyword%");
                        });
                });
            }

            $data = $query->paginate($items);

            return response()->json([
                "message" => "Doc Google Author retrieved successfully",
                "status" => true,
                "data" => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Failed to get doc google author",
                "status" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    public static function getDocGoogleAuthorById($id)
    {
        try {
            $data = DocGoogleAuthor::find($id);

            if (!$data) {
                return response()->json([
                    "message" => "Doc Google Author not found",
                    "status" => false
                ], 404);
            }

            return response()->json([
                "message" => "Success to get doc google author",
                "status" => true,
                "data" => $data
            ]);
        } catch (Exception $th) {
            return response()->json([
                "message" => "Failed to get doc google author",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }


    public static function getDocGoogleAuthorByAuthorId($authorId)
    {
        try {
            $data = DocGoogleAuthor::where('author_id', $authorId)->get();

            if ($data->isEmpty()) {
                return response()->json([
                    "message" => "Doc Google Author not found",
                    "status" => false
                ], 404);
            }

            return response()->json([
                "message" => "Success to get doc google authors by author_id",
                "status" => true,
                "data" => $data
            ]);
        } catch (Exception $th) {
            return response()->json([
                "message" => "Failed to get doc google authors by author_id",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }

    public static function createDocGoogleAuthor(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'author_id' => 'required|string',
                'title' => 'required|string|max:255',
                'abstract' => 'nullable',
                'authors' => 'required|string|max:255',
                'journal_name' => 'required|string|max:255',
                'publish_year' => 'required|integer|min:1000|max:9999',
                'citation' => 'nullable',
                'url' => 'required|max:255',
            ]);

            $validatedData['abstract'] = $validatedData['abstract'] ?? '';

            $validatedData['citation'] = (int) ($validatedData['citation'] ?: 0);

            $docGoogleAuthorData = DocGoogleAuthor::create($validatedData);

            return response()->json([
                "message" => "Doc Google Author created successfully",
                "status" => true,
                "data" => $docGoogleAuthorData
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "message" => "Validation failed",
                "status" => false,
                "errors" => $e->errors()
            ], 422);
        } catch (Exception $th) {
            return response()->json([
                "message" => "Failed to create doc google author",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }

    public static function updateDocGoogleAuthor(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'author_id' => 'sometimes|required',
                'title' => 'sometimes|required|string|max:255',
                'abstract' => 'nullable|string',
                'authors' => 'sometimes|required|string|max:255',
                'journal_name' => 'sometimes|required|string|max:255',
                'publish_year' => 'sometimes|required|integer|min:1000|max:9999',
                'citation' => 'nullable|integer',
                'url' => 'sometimes|required|max:255',
            ]);

            $docGoogleAuthorData = DocGoogleAuthor::findOrFail($id);


            if ($request->has('citation')) {
                $validatedData['citation'] = (int) ($validatedData['citation'] ?: 0);
            }

            $docGoogleAuthorData->update($validatedData);
            $updatedData = DocGoogleAuthor::findOrFail($id);

            return response()->json([
                'message' => 'Doc Google Author updated successfully',
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
                'message' => 'Failed to update the Doc Google Author',
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


    public static function deleteDocGoogleAuthor($id)
    {
        try {
            $data = DocGoogleAuthor::find($id);

            if (!$data) {
                return response()->json([
                    "message" => "Doc Google Author not found",
                    "status" => false
                ], 404);
            }

            $data->delete();

            return response()->json([
                "message" => "Doc Google Author deleted successfully",
                "status" => true
            ]);
        } catch (Exception $th) {
            return response()->json([
                "message" => "Failed to delete doc google author",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }

    public static function syncFromSintaDocGoogleAuthor()
    {
        try {
            $tokenSinta = LoginSintaService::loginSinta(new Request());
            $authors = ProfileAuthor::select('NIDN')->distinct()->get();
            $fetchedDocuments = [];

            foreach ($authors as $author) {
                $totalDocumentsFetched = 0;
                $processedId = [];

                $sintaApiUrl = "http://apisinta.kemdikbud.go.id/v3/dev/271071775/author/google/nidn/{$author->NIDN}";

                $response = Http::withToken($tokenSinta)->post($sintaApiUrl);

                if ($response->failed()) {
                    return response()->json([
                        "message" => "Failed to fetch google doc data from SINTA for NIDN {$author->NIDN}",
                        "status" => false,
                        "error" => $response->body()
                    ], 400);
                }

                $data = $response->json();
                $documents = $data['results']['documents'];
                $totalDocuments = $data['results']['total'];

                foreach ($documents as $document) {
                    $id = $document['id'];

                    if (!in_array($id, $processedId)) {
                        $processedId[] = $id;

                        $citation = ($document['citation'] === "") ? 0 : (int) $document['citation'];

                        $docGoogleAuthor = DocGoogleAuthor::updateOrCreate(
                            [
                                'id' => $document['id'],
                            ],
                            [
                                'title' => $document['title'] ?? '',
                                'author_id' => $data['results']['authors']['author_id'],
                                'abstract' => $document['abstract'] ?? '',
                                'authors' => $document['authors'] ?? '',
                                'journal_name' => $document['journal_name'] ?? '',
                                'publish_year' => $document['publish_year'] ?? 0,
                                'citation' => $citation,
                                'url' => $document['url'] ?? '',
                            ]
                        );

                        $fetchedDocuments[] = $docGoogleAuthor;
                        $totalDocumentsFetched++;
                    }
                }

                if ($totalDocumentsFetched >= $totalDocuments) {
                    break;
                }
            }

            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Google Author',
                'status' => 'success',
            ]);

            return response()->json([
                "message" => "Synced doc google author data from SINTA successfully",
                "status" => true,
                "data" => $fetchedDocuments
            ], 200);
        } catch (\Throwable $th) {
            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Google Author',
                'status' => 'failed',
            ]);
            
            return response()->json([
                "message" => "Failed to sync doc google author data from SINTA",
                "status" => false,
                "error" => $th->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DocWosAuthor;
use App\Models\LogSyncSinta;
use App\Models\ProfileAuthor;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;

class DocWosAuthorService
{
    public static function getDocWosAuthor()
    {
        try {
            $data = DocWosAuthor::all()->makeHidden(['author_id', 'created_at', 'updated_at']);

            return response()->json([
                'message' => 'Success to get all data',
                'status' => true,
                'data' => $data,
            ]);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'Failed to get all data',
                'status' => false,
                'error' => $th->getMessage(),
            ]);
        }
    }

    public static function getPaginateDocWosAuthor(Request $request)
    {
        try {
            $keyword = $request->query('keyword');
            $publishYear = $request->query('year');
            $perPage = $request->query('items', 10);

            $docWosAuthorQuery = DocWosAuthor::query()
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('title', 'like', "%$keyword%")
                        ->orWhere('authors', 'like', "%$keyword%")
                        ->orWhereHas('profileAuthor', function ($query) use ($keyword) {
                            $query->where('fullname', 'like', "%$keyword%");
                        });
                })
                ->when($publishYear, function ($query, $year) {
                    return $query->where('publish_date', $year);
                });

            $data = $docWosAuthorQuery->with('profileAuthor')->paginate($perPage);
            collect($data->items())->transform(function ($item) {
                return $item->makeHidden(['author_id', 'created_at', 'updated_at']);
            });

            return response()->json(
                [
                    'message' => 'DocWosAuthor retrieved successfully',
                    'status' => true,
                    'data' => $data,
                ],
                200,
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Failed to retrieve DocWosAuthor',
                    'status' => false,
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public static function getDocWosAuthorById($id)
    {
        try {
            $data = DocWosAuthor::where('id', $id)->get();

            if ($data->isNotEmpty()) {
                $data->makeHidden(['author_id', 'created_at', 'updated_at']);
            }

            if ($data->isEmpty()) {
                return response()->json(
                    [
                        'message' => 'Doc wos author not found',
                        'status' => false,
                    ],
                    404,
                );
            }

            return response()->json([
                'message' => 'Success to get doc wos author by id',
                'status' => true,
                'data' => $data,
            ]);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'Failed to get doc wos author by id',
                'status' => false,
                'error' => $th->getMessage(),
            ]);
        }
    }

    public static function getDocWosAuthorByAuthorId($author_id)
    {
        try {
            $data = DocWosAuthor::where('author_id', $author_id)
                ->get()
                ->makeHidden(['author_id', 'created_at', 'updated_at']);

            if ($data->isEmpty()) {
                return response()->json(
                    [
                        'message' => 'Doc wos author not found',
                        'status' => false,
                    ],
                    404,
                );
            }

            return response()->json([
                'message' => 'Success to get doc wos author by author id',
                'status' => true,
                'data' => $data,
            ]);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'Failed to get doc wos author by author id',
                'status' => false,
                'error' => $th->getMessage(),
            ]);
        }
    }

    public static function createDocWosAuthor(Request $request)
    {
        try {
            $validateData = $request->validate([
                'id' => 'required|integer',
                'author_id' => 'required|integer',
                'publons_id' => 'required|integer',
                'wos_id' => 'required|string|max:255',
                'doi' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'first_author' => 'required|string|max:255',
                'last_author' => 'required|string|max:255',
                'authors' => 'required|string',
                'publish_date' => 'required|string|max:255',
                'journal_name' => 'required|string|max:255',
                'citation' => 'required|integer',
                'abstract' => 'required|string',
                'publish_type' => 'required|string|max:255',
                'publish_year' => 'required|integer',
                'page_begin' => 'required|integer',
                'page_end' => 'required|integer',
                'issn' => 'required|string|max:255',
                'eissn' => 'required|string|max:255',
                'url' => 'required|string|max:255',
            ]);

            $data = DocWosAuthor::create($validateData);
            $responseData = $data->makeHidden(['author_id', 'created_at', 'updated_at'])->toArray();

            return response()->json([
                'message' => 'doc wos author created successfully',
                'status' => true,
                'data' => $responseData,
            ]);
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'message' => 'Validation failed',
                    'status' => false,
                    'errors' => $e->errors(),
                ],
                422,
            );
        } catch (Exception $th) {
            return response()->json(
                [
                    'message' => 'Failed to create doc wos author',
                    'status' => false,
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    public static function updateDocWosAuthor(Request $request, $id)
    {
        try {
            $validateData = $request->validate([
                'author_id' => 'required|integer',
                'publons_id' => 'required|integer',
                'wos_id' => 'required|string|max:255',
                'doi' => 'required|string|max:255',
                'title' => 'required|string|max:255',
                'first_author' => 'required|string|max:255',
                'last_author' => 'required|string|max:255',
                'authors' => 'required|string',
                'publish_date' => 'required|string|max:255',
                'journal_name' => 'required|string|max:255',
                'citation' => 'required|integer',
                'abstract' => 'required|string',
                'publish_type' => 'required|string|max:255',
                'publish_year' => 'required|integer',
                'page_begin' => 'required|integer',
                'page_end' => 'required|integer',
                'issn' => 'required|string|max:255',
                'eissn' => 'required|string|max:255',
                'url' => 'required|string|max:255',
            ]);

            $data = DocWosAuthor::findOrFail($id);
            $data->update($validateData);
            $updatedData = $data->makeHidden(['author_id', 'created_at', 'updated_at']);

            return response()->json([
                'message' => 'doc wos author updated successfully',
                'status' => true,
                'data' => $updatedData,
            ]);
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'message' => 'Validation failed',
                    'status' => false,
                    'errors' => $e->errors(),
                ],
                422,
            );
        } catch (Exception $th) {
            return response()->json(
                [
                    'message' => 'Failed to update doc wos author',
                    'status' => false,
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    public static function deleteDocWosAuthor($id)
    {
        try {
            $data = DocWosAuthor::findOrFail($id);

            $data->delete();

            return response()->json([
                'message' => 'doc wos author deleted successfully',
                'status' => true,
            ]);
        } catch (Exception $th) {
            return response()->json([
                'message' => 'Failed to delete doc wos author',
                'status' => false,
                'error' => $th->getMessage(),
            ]);
        }
    }

    public static function syncFromSintaDocWosAuthor()
    {
        try {
            $tokenSinta = LoginSintaService::loginSinta(new Request());
            $authors = ProfileAuthor::select('NIDN')->distinct()->get();

            foreach ($authors as $author) {
                $sintaApiUrl = "http://apisinta.kemdikbud.go.id/v3/dev/271071775/author/wos/nidn/{$author->NIDN}";

                $response = Http::withToken($tokenSinta)->post($sintaApiUrl);

                $jsonData = json_decode($response->getBody(), true);
                if (isset($jsonData['results'])) {
                    $authorId = $jsonData['results']['authors']['author_id'];
                    $documents = $jsonData['results']['documents'];

                    foreach ($documents as $doc) {
                        DocWosAuthor::updateOrCreate(
                            [
                                'id' => $doc['id'],
                                'author_id' => $authorId,
                                'doi' => $doc['doi'],
                                'wos_id' => $doc['wos_id'],
                                'title' => $doc['title'],
                            ],
                            [
                                'publons_id' => !empty($doc['publons_id']) ? $doc['publons_id'] : 0,
                                'first_author' => !empty($doc['first_author']) ? $doc['first_author'] : '',
                                'last_author' => !empty($doc['last_author']) ? $doc['last_author'] : '',
                                'authors' => !empty($doc['authors']) ? $doc['authors'] : '',
                                'publish_date' => !empty($doc['publish_date']) ? $doc['publish_date'] : '',
                                'journal_name' => !empty($doc['journal_name']) ? $doc['journal_name'] : '',
                                'citation' => !empty($doc['citation']) ? $doc['citation'] : 0,
                                'abstract' => !empty($doc['abstract']) ? $doc['abstract'] : '',
                                'publish_type' => !empty($doc['publish_type']) ? $doc['publish_type'] : '',
                                'publish_year' => !empty($doc['publish_year']) ? $doc['publish_year'] : 0,
                                'page_begin' => !empty($doc['page_begin']) ? $doc['page_begin'] : 0,
                                'page_end' => !empty($doc['page_end']) ? $doc['page_end'] : 0,
                                'issn' => !empty($doc['issn']) ? $doc['issn'] : '',
                                'eissn' => !empty($doc['eissn']) ? $doc['eissn'] : '',
                                'url' => !empty($doc['url']) ? $doc['url'] : '',
                            ],
                        );
                    }
                }
            }

            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Wos Author',
                'status' => 'success',
            ]);

            return response()->json([
                "message" => "Synced doc wos author data from SINTA successfully",
                "status" => true,
                "data" => DocWosAuthor::all()->makeHidden(['author_id', 'created_at', 'updated_at']),
            ], 200);
        } catch (Exception $th) {
            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Wos Author',
                'status' => 'failed',
            ]);
            
            return response()->json([
                "message" => "Failed to sync doc wos author data from SINTA",
                "status" => false,
                "error" => $th->getMessage(),
            ], 500);
        }
    }
}

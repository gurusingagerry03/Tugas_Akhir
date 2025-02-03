<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\DocBookAuthor;
use App\Models\LogSyncSinta;
use App\Models\ProfileAuthor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class DocBookAuthorService
{
    public static function getAllBookDocService()
    {
        try {
            $BookDoc = DocBookAuthor::all();

            return response()->json([
                'message' => 'BookDoc retrieved successfully',
                'status' => 'true',
                'data' => $BookDoc,
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'message' => 'Failed to retrieve BookDoc',
                    'status' => 'false',
                    'error' => $th->getMessage(),
                ],
                200,
            );
        }
    }

    public static function getPaginateBookDocService(Request $request)
    {
        try {
            $keyword = $request->query('keyword');
            $year = $request->query('year');
            $category = $request->query('category');
            $items = $request->query('items', 10);

            $docBookAuthor = DocBookAuthor::with([
                'profileAuthor' => function ($query) {
                    $query->select('id', 'nidn', 'fullname', 'gelar_depan', 'gelar_belakang', 'country', 'image', 'programs_id');
                },
                'profileAuthor.program' => function ($query) {
                    $query->select('code_pddikti', 'faculty_id', 'name_id', 'name_en');
                },
            ])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%")
                        ->orWhere('authors', 'like', "%$keyword%")
                        ->orWhereHas('profileAuthor', function ($q) use ($keyword) {
                            $q->where('fullname', 'like', "%$keyword%");
                        });
                });
            })
                ->when($year, function ($query, $year) {
                    return $query->where('year', $year);
                })
                ->when($category, function ($query, $category) {
                    return $query->where('category', 'like', "%$category%");
                })
                ->paginate($items);

            return response()->json([
                'message' => 'Doc Book Author retrieved successfully',
                'status' => true,
                'data' => $docBookAuthor,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve Doc Book Author',
                'status' => false,
                'error' => $th->getMessage(),
            ]);
        }
    }


    public static function getDocBookAuthorById($id)
    {
        try {
            $docBookAuthor = DocBookAuthor::findOrFail($id);

            return response()->json([
                'message' => 'DocBookAuthor retrieved successfully',
                'status' => 'true',
                'data' => $docBookAuthor,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve BookDoc',
                'status' => 'false',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public static function getDocBookAuthorByAuthorId($authorId)
    {
        try {
            $docBookAuthor = DocBookAuthor::where('author_id', $authorId)->get();

            if ($docBookAuthor->isEmpty()) {
                return response()->json([
                    'message' => 'No BookDocs found for the provided author ID.',
                    'status' => 'false',
                    'data' => [],
                ]);
            }

            return response()->json([
                'message' => 'DocBookAuthor retrieved successfully',
                'status' => 'true',
                'data' => $docBookAuthor,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve BookDoc',
                'status' => 'false',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public static function createBookDocService(Request $request)
    {
        try {
            $validated = $request->validate([
                'author_id' => 'required|int',
                'category' => 'required|string',
                'isbn' => 'required|int',
                'title' => 'required|string',
                'authors' => 'required|string',
                'place' => 'required|string',
                'publisher' => 'required|string',
                'year' => 'required|int',
            ]);
            $validate = DocBookAuthor::create($validated);

            return response()->json([
                'message' => 'BookDoc created successfully',
                'status' => 'true',
                'data' => [
                    'author_id' => $validate->author_id,
                    'category' => $validate->category,
                    'isbn' => $validate->isbn,
                    'title' => $validate->title,
                    'authors' => $validate->authors,
                    'place' => $validate->place,
                    'publisher' => $validate->publisher,
                    'year' => $validate->year,
                    'updated_at' => $validate->updated_at,
                    'created_at' => $validate->created_at,
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'message' => 'Validation Error',
                    'status' => 'false',
                    'error' => $e->errors(),
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'message' => 'Failed to create BookDoc',
                    'status' => 'false',
                    'error' => $th->getMessage(),
                ],
                200,
            );
        }
    }

    public static function updateBookDocService(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'author_id' => 'required|int',
                'category' => 'required|string',
                'isbn' => 'required|int',
                'title' => 'required|string',
                'authors' => 'required|string',
                'place' => 'required|string',
                'publisher' => 'required|string',
                'year' => 'required|int',
            ]);
            $bookDoc = DocBookAuthor::findOrFail($id);
            $bookDoc->update($validated);

            return response()->json(
                [
                    'message' => 'BookDoc updated successfully',
                    'status' => 'true',
                    'data' => $bookDoc,
                ],
                200,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'message' => 'Validation Error',
                    'status' => 'false',
                    'error' => $e->errors(),
                ],
                200,
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Failed to update BookDoc',
                    'status' => 'false',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public static function deleteBookDocService(string $id)
    {
        try {
            $bookDoc = DocBookAuthor::findOrFail($id);
            $bookDoc->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'BookDoc deleted successfully',
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'message' => 'Failed to delete BookDoc',
                    'status' => 'false',
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    public static function syncFromSintaDocBookAuthor()
    {
        try {
            $tokenSinta = LoginSintaService::loginSinta(new Request());
            $authors = ProfileAuthor::select('NIDN')->distinct()->get();
            $fetchedData = [];

            foreach ($authors as $author) {
                $totalDocumentsFetched = 0;
                $processedIsbn = [];
                $processedTitle = [];

                $sintaApi = "http://apisinta.kemdikbud.go.id/v3/dev/271071775/author/book/nidn/{$author->NIDN}";
                $res = Http::withToken($tokenSinta)->post($sintaApi);

                if ($res->failed()) {
                    return response()->json(
                        [
                            'message' => "Failed to Sync Doc Book Author from SINTA with NIDN: {$author->NIDN}",
                            'status' => false,
                            'error' => $res->body(),
                        ],
                        400,
                    );
                }

                $data = $res->json();
                $docBookAuthors = $data['results']['documents'];
                $totalDocuments = $data['results']['total'];

                foreach ($docBookAuthors as $docBookAuthor) {
                    $isbn = $docBookAuthor['isbn'];
                    $title = $docBookAuthor['title'];

                    if (!in_array($isbn, $processedIsbn) && !in_array($title, $processedTitle)) {
                        $processedIsbn[] = $isbn;
                        $processedTitle[] = $title;

                        $docAuthor = DocBookAuthor::updateOrCreate(
                            [
                                'id' => $docBookAuthor['id'],
                                'author_id' => $data['results']['authors']['author_id'],
                                'isbn' => $isbn,
                                'title' => $title,
                            ],
                            [
                                'category' => $docBookAuthor['category'] ?? '',
                                'authors' => $docBookAuthor['authors'] ?? '',
                                'place' => $docBookAuthor['place'] ?? '',
                                'publisher' => $docBookAuthor['publisher'] ?? '',
                                'year' => $docBookAuthor['year'] ?? '',
                            ]
                        );

                        $fetchedData[] = $docAuthor;
                        $totalDocumentsFetched++;
                    }
                }

                if ($totalDocumentsFetched >= $totalDocuments) {
                    break;
                }
            }

            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Book Author',
                'status' => 'success',
            ]);

            return response()->json([
                'message' => 'Synced doc book author data from SINTA successfully',
                'status' => true,
                'data' => $fetchedData,
            ], 200);
        } catch (\Throwable $th) {
            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Book Author',
                'status' => 'failed',
            ]);
            
            return response()->json([
                'message' => 'Failed to sync doc book author data from SINTA',
                'status' => 'false',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\DocScopusAuthor;
use App\Models\LogSyncSinta;
use App\Models\ProfileAuthor;
use Illuminate\Support\Facades\DB;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DocScopusAuthorService
{
    public function getAllDocScopusAuthor()
    {
        try {
            $documents = DocScopusAuthor::all()->map(function ($doc) {
                return [
                    'id' => (string) $doc->id,
                    'quartile' => (string) $doc->quartile,
                    'title' => $doc->title,
                    'publication_name' => $doc->publication_name,
                    'creator' => $doc->creator,
                    'page' => $doc->page,
                    'issn' => (string) $doc->issn,
                    'volume' => (string) $doc->volume,
                    'cover_date' => $doc->cover_date,
                    'cover_display_date' => $doc->cover_display_date,
                    'doi' => $doc->doi,
                    'citedby_count' => (string) $doc->citedby_count,
                    'aggregation_type' => $doc->aggregation_type,
                    'url' => $doc->url,
                ];
            });

            return [
                'message' => 'success to get all DocScopusAuthor',
                'status' => true,
                'data' => $documents,
            ];
        } catch (Exception $th) {
            return [
                'message' => 'failed to get all DocScopusAuthor',
                'status' => false,
                'error' => $th->getMessage(),
            ];
        }
    }

    public function getDocScopusAuthorById($id)
    {
        try {
            $docScopusAuthor = DocScopusAuthor::findOrFail($id);
            $docScopusAuthorData = [
                'id' => (string) $docScopusAuthor->id,
                'quartile' => (string) $docScopusAuthor->quartile,
                'title' => $docScopusAuthor->title,
                'publication_name' => $docScopusAuthor->publication_name,
                'creator' => $docScopusAuthor->creator,
                'page' => $docScopusAuthor->page,
                'issn' => (string) $docScopusAuthor->issn,
                'volume' => (string) $docScopusAuthor->volume,
                'cover_date' => $docScopusAuthor->cover_date,
                'cover_display_date' => $docScopusAuthor->cover_display_date,
                'doi' => $docScopusAuthor->doi,
                'citedby_count' => (string) $docScopusAuthor->citedby_count,
                'aggregation_type' => $docScopusAuthor->aggregation_type,
                'url' => $docScopusAuthor->url,
            ];
            return response()->json([
                'message' => 'Success to get Doc Scopu
                s Author',
                'status' => true,
                'data' => $docScopusAuthorData,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Doc Scopus Author not found',
                'status' => false,
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to get Doc Scopus Author',
                'status' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function getPaginateDocScopusAuthor(Request $request)
    {
        try {
            $query = DocScopusAuthor::query()
                ->when($request->query('faculty'), function ($query) use ($request) {
                    $query->whereHas('author.program', function ($q) use ($request) {
                        $q->where('faculty_id', $request->query('faculty'));
                    });
                })
                ->when($request->query('keyword'), function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('title', 'like', '%' . $request->query('keyword') . '%')
                            ->orWhere('creator', 'like', '%' . $request->query('keyword') . '%')
                            ->orWhere('publication_name', 'like', '%' . $request->query('keyword') . '%')
                            ->orWhereHas('author', function ($q) use ($request) {
                                $q->where('fullname', 'like', '%' . $request->query('keyword') . '%');
                            });
                    });
                })
                ->when($request->query('author'), function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('creator', 'like', '%' . $request->query('author') . '%');
                    });
                })
                ->when($request->query('year'), function ($query) use ($request) {
                    $query->whereYear('cover_date', $request->query('year'));
                });

            $results = $query->with('author')->paginate($request->query('items', 10));

            $results->through(function ($doc) {
                return [
                    'id' => (string) $doc->id,
                    'quartile' => (string) $doc->quartile,
                    'title' => $doc->title,
                    'publication_name' => $doc->publication_name,
                    'creator' => $doc->creator,
                    'page' => $doc->page,
                    'issn' => (string) $doc->issn,
                    'volume' => (string) $doc->volume,
                    'cover_date' => $doc->cover_date,
                    'cover_display_date' => $doc->cover_display_date,
                    'doi' => $doc->doi,
                    'citedby_count' => (string) $doc->citedby_count,
                    'aggregation_type' => $doc->aggregation_type,
                    'url' => $doc->url,
                    'profile_author' => $doc->author
                ];
            });

            return [
                'message' => 'DocScopusAuthor retrieved successfully',
                'status' => true,
                'data' => $results,
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Failed to retrieve DocScopusAuthor',
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getByAuthorId($authorId)
    {
        try {
            $documents = DocScopusAuthor::where('author_id', $authorId)
                ->get()
                ->map(function ($doc) {
                    return [
                        'id' => (string) $doc->id,
                        'quartile' => (string) $doc->quartile,
                        'title' => $doc->title,
                        'publication_name' => $doc->publication_name,
                        'creator' => $doc->creator,
                        'page' => $doc->page,
                        'issn' => (string) $doc->issn,
                        'volume' => (string) $doc->volume,
                        'cover_date' => $doc->cover_date,
                        'cover_display_date' => $doc->cover_display_date,
                        'doi' => $doc->doi,
                        'citedby_count' => (string) $doc->citedby_count,
                        'aggregation_type' => $doc->aggregation_type,
                        'url' => $doc->url,
                    ];
                })
                ->toArray();

            return [
                'message' => 'DocScopusAuthor retrieved successfully',
                'status' => true,
                'data' => $documents,
            ];
        } catch (Exception $e) {
            \Log::error('Error getting DocScopusAuthor by author ID: ' . $e->getMessage());
            return [
                'message' => 'Failed to get DocScopusAuthor by Author ID',
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function createDocScopusAuthor(Request $request)
    {
        try {
            $validated = $request->validate([
                'author_id' => 'required|integer',
                'quartile' => 'required|integer',
                'title' => 'required|string',
                'publication_name' => 'required|string',
                'creator' => 'required|string',
                'page' => 'required|string',
                'issn' => 'required|integer',
                'volume' => 'required|integer',
                'cover_date' => 'required|date',
                'cover_display_date' => 'required|string',
                'doi' => 'required|string',
                'citedby_count' => 'required|integer',
                'aggregation_type' => 'required|string',
                'url' => 'required|url',
            ]);

            DB::beginTransaction();
            try {
                $maxId = DocScopusAuthor::max('id') ?? 850000;
                $nextId = $maxId + 1;

                $docScopusAuthor = new DocScopusAuthor();
                $docScopusAuthor->id = $nextId;
                $docScopusAuthor->fill($validated);
                $docScopusAuthor->save();

                DB::commit();

                return [
                    'message' => 'DocScopusAuthor created successfully',
                    'status' => true,
                    'data' => $docScopusAuthor,
                ];
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            \Log::error('Error creating DocScopusAuthor: ' . $e->getMessage());
            return [
                'message' => 'Failed to create DocScopusAuthor',
                'status' => false,
                'error' => $e->errors(),
            ];
        }
    }

    public function updateDocScopusAuthor(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'quartile' => 'required|integer',
                'title' => 'required|string',
                'publication_name' => 'required|string',
                'creator' => 'required|string',
                'page' => 'required|string',
                'issn' => 'required|integer',
                'volume' => 'required|integer',
                'cover_date' => 'required|date',
                'cover_display_date' => 'required|string',
                'doi' => 'required|string',
                'citedby_count' => 'required|integer',
                'aggregation_type' => 'required|string',
                'url' => 'required|url',
            ]);

            DB::beginTransaction();
            try {
                $docScopusAuthor = DocScopusAuthor::findOrFail($id);
                $docScopusAuthor->update($validated);

                DB::commit();

                return [
                    'message' => 'DocScopusAuthor updated successfully',
                    'status' => true,
                    'data' => [
                        'id' => (string) $docScopusAuthor->id,
                        'quartile' => (string) $docScopusAuthor->quartile,
                        'title' => $docScopusAuthor->title,
                        'publication_name' => $docScopusAuthor->publication_name,
                        'creator' => $docScopusAuthor->creator,
                        'page' => $docScopusAuthor->page,
                        'issn' => (string) $docScopusAuthor->issn,
                        'volume' => (string) $docScopusAuthor->volume,
                        'cover_date' => $docScopusAuthor->cover_date,
                        'cover_display_date' => $docScopusAuthor->cover_display_date,
                        'doi' => $docScopusAuthor->doi,
                        'citedby_count' => (string) $docScopusAuthor->citedby_count,
                        'aggregation_type' => $docScopusAuthor->aggregation_type,
                        'url' => $docScopusAuthor->url,
                    ],
                ];
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ValidationException $e) {
            return [
                'message' => 'Validation failed',
                'status' => false,
                'error' => $e->errors(),
            ];
        } catch (Exception $e) {
            \Log::error('Error updating DocScopusAuthor: ' . $e->getMessage());
            return [
                'message' => 'Failed to update DocScopusAuthor',
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function deleteDocScopusAuthor($id)
    {
        try {
            DB::beginTransaction();
            try {
                $docScopusAuthor = DocScopusAuthor::findOrFail($id);
                $docScopusAuthor->delete();

                DB::commit();

                return [
                    'message' => 'DocScopusAuthor deleted successfully',
                    'status' => true,
                ];
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            \Log::error('Error deleting DocScopusAuthor: ' . $e->getMessage());
            return [
                'message' => 'Failed to delete DocScopusAuthor',
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function syncFromSinta()
    {
        try {
            $tokenSinta = LoginSintaService::loginSinta(new Request());
            $authors = ProfileAuthor::select('nidn')->distinct()->get();
            $syncedDocs = [];

            foreach ($authors as $author) {
                if (empty($author->nidn)) {
                    continue;
                }

                try {
                    $response = Http::withToken($tokenSinta)->post("http://apisinta.kemdikbud.go.id/v3/dev/271071775/author/scopus/nidn/{$author->nidn}");

                    if ($response->failed()) {
                        \Log::warning("Failed to get response from SINTA for NIDN: {$author->nidn}");
                        continue;
                    }

                    $jsonData = $response->json();
                    if (empty($jsonData['results']['documents']) || empty($jsonData['results']['authors']['author_id'])) {
                        continue;
                    }

                    $authorId = $jsonData['results']['authors']['author_id'];

                    foreach ($jsonData['results']['documents'] as $doc) {
                        DB::beginTransaction();
                        try {
                            $docScopusAuthor = DocScopusAuthor::updateOrCreate(
                                ['id' => (int) $doc['id']],
                                [
                                    'author_id' => $authorId,
                                    'quartile' => !empty($doc['quartile']) ? (int) $doc['quartile'] : 0,
                                    'title' => $doc['title'] ?? '',
                                    'publication_name' => $doc['publication_name'] ?? '',
                                    'creator' => $doc['creator'] ?? '',
                                    'page' => $doc['page'] ?? '',
                                    'issn' => !empty($doc['issn']) ? (int) $doc['issn'] : 0,
                                    'volume' => !empty($doc['volume']) ? (int) $doc['volume'] : 0,
                                    'cover_date' => $doc['cover_date'] ?? null,
                                    'cover_display_date' => $doc['cover_display_date'] ?? '',
                                    'doi' => $doc['doi'] ?? '',
                                    'citedby_count' => !empty($doc['citedby_count']) ? (int) $doc['citedby_count'] : 0,
                                    'aggregation_type' => $doc['aggregation_type'] ?? '',
                                    'url' => $doc['url'] ?? '',
                                ],
                            );

                            $syncedDocs[] = [
                                'id' => (string) $docScopusAuthor->id,
                                'quartile' => (string) $docScopusAuthor->quartile,
                                'title' => $docScopusAuthor->title,
                                'publication_name' => $docScopusAuthor->publication_name,
                                'creator' => $docScopusAuthor->creator,
                                'page' => $docScopusAuthor->page,
                                'issn' => (string) $docScopusAuthor->issn,
                                'volume' => (string) $docScopusAuthor->volume,
                                'cover_date' => $docScopusAuthor->cover_date,
                                'cover_display_date' => $docScopusAuthor->cover_display_date,
                                'doi' => $docScopusAuthor->doi,
                                'citedby_count' => (string) $docScopusAuthor->citedby_count,
                                'aggregation_type' => $docScopusAuthor->aggregation_type,
                                'url' => $docScopusAuthor->url,
                            ];

                            DB::commit();
                        } catch (Exception $e) {
                            DB::rollBack();
                            \Log::error('Error syncing document: ' . $e->getMessage());
                        }
                    }
                } catch (Exception $e) {
                    \Log::error("Error processing author {$author->nidn}: " . $e->getMessage());
                    continue;
                }
            }

            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Scopus Author',
                'status' => 'success',
            ]);

            return response()->json([
                "message" => "Synced doc scopus author data from SINTA",
                "status" => true,
                "data" => $syncedDocs,
            ], 200);
        } catch (\Throwable $th) {
            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Scopus Author',
                'status' => 'failed',
            ]);
            
            return response()->json([
                "message" => "Failed to sync doc scopus author data from SINTA",
                "status" => false,
                "error" => $th->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Services;

use App\Exports\ExportDocCommunityServiceAuthor;
use App\Imports\ImportDocCommunityServiceAuthor;
use App\Models\DocCommunityserviceAuthor;
use App\Models\LogSyncSinta;
use Illuminate\Http\Request;
use App\Models\ProfileAuthor;
use Illuminate\Support\Facades\Http;
use App\Models\MemberCommunityservice;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Log;
use Maatwebsite\Excel\Facades\Excel;

class DocCommunityserviceAuthorService
{
    public static function transformDocCommunityserviceAuthor($doc)
    {
        return [
            'id'                    => $doc->id,
            'leader'                => $doc->leader,
            'leader_nidn'           => $doc->leader_nidn,
            'title'                 => $doc->title,
            'first_proposed_year'   => $doc->first_proposed_year,
            'proposed_year'         => $doc->proposed_year,
            'implementation_year'   => $doc->implementation_year,
            'focus'                 => $doc->focus,
            'funds_approved'        => $doc->funds_approved,
            'scheme'                => [
                'id'      => '',
                'abbrev'  => !empty($doc->scheme_abbrev) ? $doc->scheme_abbrev : '',
                'name'    => !empty($doc->scheme_name) ? $doc->scheme_name : '',
            ],
            "kategori_sumber_dana" => '',
            "negara_sumber_dana"   => '',
            "sumber_dana"         => '',
            "sumber_data"         => '',
            "kd_program_hibah"    => '',
            "program_hibah"       => '',
            'member'               => $doc->members->filter(function ($member) use ($doc) {
                return $member->nidn !== $doc->leader_nidn;
            })->map(function ($member) {
                return [
                    'author_id' => $member->author_id,
                    'nidn'      => $member->nidn,
                    'nama'      => $member->name,
                ];
            })->values()->toArray(),
        ];
    }

    public function getAllDocCommunityserviceAuthor()
    {
        try {
            $data = DocCommunityserviceAuthor::with('members')->get();

            $responseData = $data->map(function ($doc) {
                return self::transformDocCommunityserviceAuthor($doc);
            });

            return [
                'message' => 'DocCommunityserviceAuthor retrieved successfully',
                'status' => true,
                'data' => $responseData
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Failed to retrieve DocCommunityserviceAuthor',
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getPaginateDocCommunityserviceAuthor(Request $request)
    {
        try {
            $keyword = $request->query('keyword');
            $faculty = $request->query('faculty');
            $firstProposedYear = $request->query('first_proposed_year');
            $proposedYear = $request->query('proposed_year');
            $implementationYear = $request->query('implementation_year');
            $perPage = $request->query('items', 10);

            $query = DocCommunityserviceAuthor::with(['author.program.faculty', 'members'])
                ->when($faculty, function ($query) use ($faculty) {
                    $query->whereHas('author.program', function ($q) use ($faculty) {
                        $q->where('faculty_id', $faculty);
                    });
                })
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('title', 'like', "%$keyword%")
                            ->orWhere('leader', 'like', "%$keyword%")
                            ->orWhere('focus', 'like', "%$keyword%")
                            ->orWhereHas('author', function ($q) use ($keyword) {
                                $q->where('fullname', 'like', "%$keyword%");
                            })
                            ->orWhereHas('members', function ($q) use ($keyword) {
                                $q->where('name', 'like', "%$keyword%");
                            });
                    });
                })
                ->when($firstProposedYear, function ($query, $year) {
                    return $query->where('first_proposed_year', $year);
                })
                ->when($proposedYear, function ($query, $year) {
                    return $query->where('proposed_year', $year);
                })
                ->when($implementationYear, function ($query, $year) {
                    return $query->where('implementation_year', $year);
                });

            $data = $query->paginate($perPage);

            $transformedData = $data->getCollection()->map(function ($doc) {
                return self::transformDocCommunityserviceAuthor($doc);
            });

            $data->setCollection($transformedData);

            return [
                'message' => 'DocCommunityserviceAuthor retrieved successfully',
                'status' => true,
                'data' => $data
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Failed to retrieve DocCommunityserviceAuthor',
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getDocCommunityserviceAuthorById($id)
    {
        try {
            $doc = DocCommunityserviceAuthor::with('members')->findOrFail($id);

            $transformedData = self::transformDocCommunityserviceAuthor($doc);

            return [
                'message' => 'DocCommunityserviceAuthor retrieved successfully',
                'status' => true,
                'data' => $transformedData
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Failed to retrieve DocCommunityserviceAuthor',
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getDocCommunityserviceAuthorByAuthorId($authorId)
    {
        try {
            $docs = DocCommunityserviceAuthor::with('members')->where('author_id', $authorId)->get();

            if ($docs->isEmpty()) {
                return response()->json([
                    'message' => 'DocCommunityserviceAuthor not found',
                    'status' => false
                ], 404);
            }

            $formattedDocs = $docs->map(function ($doc) {
                return self::transformDocCommunityserviceAuthor($doc);
            });

            return response()->json([
                'message' => 'DocCommunityserviceAuthors retrieved successfully',
                'status' => true,
                'data' => $formattedDocs
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve DocCommunityserviceAuthors',
                'status' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function createDocCommunityserviceAuthor(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'author_id' => 'required|integer',
                'leader' => 'required|string',
                'leader_nidn' => 'required|string',
                'title' => 'required|string',
                'first_proposed_year' => 'required|integer',
                'proposed_year' => 'required|integer',
                'implementation_year' => 'required|integer',
                'focus' => 'required|string',
                'funds_approved' => 'required|string',
                'scheme_abbrev' => 'nullable|string',
                'scheme_name' => 'nullable|string',
                'tkt' => 'nullable|string',
                'result_comservice' => 'nullable|string',
                'target_society_name' => 'nullable|string',
                'target_society_address' => 'nullable|string',
                'target_society_cityorregency' => 'nullable|string',
                'member' => 'required|array',
                'member.*.author_id' => 'required|string',
                'member.*.nidn' => 'required|string',
                'member.*.name' => 'required|string'
            ]);

            $docCommunityserviceAuthor = DocCommunityserviceAuthor::create($validatedData);

            foreach ($validatedData['member'] as $index => $member) {
                if (!empty($member['name'])) {
                    MemberCommunityservice::create([
                        'communityservice_id' => $docCommunityserviceAuthor->id,
                        'author_id' => $member['author_id'] ?? 0,
                        'nidn' => $member['nidn'] ?? '',
                        'name' => $member['name'],
                        'ordernum' => $index + 1
                    ]);
                }
            }

            $transformedData = self::transformDocCommunityserviceAuthor($docCommunityserviceAuthor);

            return [
                'message' => 'DocCommunityserviceAuthor created successfully',
                'status' => true,
                'data' => $transformedData
            ];
        } catch (ValidationException $e) {
            return [
                'message' => 'Validation failed',
                'status' => false,
                'error' => $e->errors()
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Failed to create DocCommunityserviceAuthor',
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateDocCommunityserviceAuthor(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'author_id' => 'required|integer',
                'leader' => 'required|string',
                'leader_nidn' => 'required|string',
                'title' => 'required|string',
                'first_proposed_year' => 'required|integer',
                'proposed_year' => 'required|integer',
                'implementation_year' => 'required|integer',
                'focus' => 'required|string',
                'funds_approved' => 'required|string',
                'scheme_abbrev' => 'nullable|string',
                'scheme_name' => 'nullable|string',
                'tkt' => 'nullable|string',
                'result_comservice' => 'nullable|string',
                'target_society_name' => 'nullable|string',
                'target_society_address' => 'nullable|string',
                'target_society_cityorregency' => 'nullable|string',
                'member' => 'required|array',
                'member.*.author_id' => 'required|string',
                'member.*.nidn' => 'required|string',
                'member.*.name' => 'required|string'
            ]);

            MemberCommunityservice::where('communityservice_id', $id)->delete();

            foreach ($validatedData['member'] as $index => $member) {
                if (!empty($member['name'])) {
                    MemberCommunityservice::create([
                        'communityservice_id' => $id,
                        'author_id' => $member['author_id'],
                        'nidn' => $member['nidn'],
                        'name' => $member['name'],
                        'ordernum' => $index + 1
                    ]);
                }
            }
            $docCommunityserviceAuthor = DocCommunityserviceAuthor::with('members')->findOrFail($id);
            $docCommunityserviceAuthor->update($validatedData);

            $transformedData = self::transformDocCommunityserviceAuthor($docCommunityserviceAuthor);

            return [
                'message' => 'DocCommunityserviceAuthor updated successfully',
                'status' => true,
                'data' => $transformedData
            ];
        } catch (ValidationException $e) {
            return [
                'message' => 'Validation failed',
                'status' => false,
                'error' => $e->errors()
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Failed to update DocCommunityserviceAuthor',
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteDocCommunityserviceAuthor($id)
    {
        try {
            $docCommunityserviceAuthor = DocCommunityserviceAuthor::findOrFail($id);

            MemberCommunityservice::where('communityservice_id', $id)->delete();

            $docCommunityserviceAuthor->delete();

            return [
                'message' => 'DocCommunityserviceAuthor deleted successfully',
                'status' => true
            ];
        } catch (Exception $e) {
            return [
                'message' => 'Failed to delete DocCommunityserviceAuthor',
                'status' => false,
                'error' => $e->getMessage()
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
                $nidn = $author->nidn;
                if (empty($nidn)) {
                    continue;
                }

                $sintaApiUrl = "http://apisinta.kemdikbud.go.id/v3/dev/271071775/author/service/nidn/{$nidn}";
                $response = Http::withToken($tokenSinta)->post($sintaApiUrl);

                if ($response->failed()) {
                    \Log::error("Failed to get response from SINTA for NIDN: {$nidn}");
                    continue;
                }

                $jsonData = $response->json();
                if (!isset($jsonData['results']['documents']) || !isset($jsonData['results']['authors']['author_id'])) {
                    \Log::warning("Invalid response structure for NIDN: {$nidn}");
                    continue;
                }

                $authorId = $jsonData['results']['authors']['author_id'];
                $documents = $jsonData['results']['documents'];

                foreach ($documents as $doc) {
                    DB::beginTransaction();
                    try {

                        $firstProposedYear = !empty($doc['first_proposed_year']) ? intval($doc['first_proposed_year']) : 0;
                        $proposedYear = !empty($doc['proposed_year']) ? intval($doc['proposed_year']) : 0;
                        $implementationYear = !empty($doc['implementation_year']) ? intval($doc['implementation_year']) : 0;
                        $fundsApproved = is_numeric($doc['funds_approved'] ?? '') ? floatval($doc['funds_approved']) : 0;


                        $communityService = DocCommunityserviceAuthor::updateOrCreate(
                            ['id' => $doc['id']],
                            [
                                'author_id' => $authorId,
                                'leader' => $doc['leader'] ?? '',
                                'leader_nidn' => $doc['leader_nidn'] ?? '',
                                'title' => $doc['title'] ?? '',
                                'first_proposed_year' => $firstProposedYear,
                                'proposed_year' => $proposedYear,
                                'implementation_year' => $implementationYear,
                                'focus' => $doc['focus'] ?? '',
                                'funds_approved' => $doc['funds_approved'],
                                'scheme_id' => $doc['scheme']['id'] ?? '',
                                'scheme_abbrev' => $doc['scheme']['abbrev'] ?? '',
                                'scheme_name' => $doc['scheme']['name'] ?? '',
                            ]
                        );

                        MemberCommunityservice::where('communityservice_id', $doc['id'])->delete();

                        if (!empty($doc['leader']) && !empty($doc['leader_nidn'])) {
                            MemberCommunityservice::create([
                                'communityservice_id' => $doc['id'],
                                'author_id' => $doc['member'][0]['author_id'] ?? $authorId,
                                'nidn' => $doc['leader_nidn'],
                                'name' => $doc['leader'],
                                'ordernum' => 1
                            ]);
                        }


                        if (isset($doc['member']) && is_array($doc['member'])) {
                            $orderNum = 2;
                            foreach ($doc['member'] as $member) {
                                if ($member['nidn'] === $doc['leader_nidn']) {
                                    continue;
                                }

                                MemberCommunityservice::create([
                                    'communityservice_id' => $doc['id'],
                                    'author_id' => $member['author_id'],
                                    'nidn' => $member['nidn'],
                                    'name' => $member['nama'],
                                    'ordernum' => $orderNum++
                                ]);
                            }
                        }
                        $communityService->load('members');
                        $formattedDoc = [
                            'id' => $communityService->id,
                            'leader' => $communityService->leader,
                            'leader_nidn' => $communityService->leader_nidn,
                            'title' => $communityService->title,
                            'first_proposed_year' => $communityService->first_proposed_year,
                            'proposed_year' => $communityService->proposed_year,
                            'implementation_year' => $communityService->implementation_year,
                            'focus' => $communityService->focus,
                            'funds_approved' => $communityService->funds_approved,
                            'scheme' => [
                                'id' => $communityService->scheme_id,
                                'abbrev' => $communityService->scheme_abbrev,
                                'name' => $communityService->scheme_name
                            ],
                            'kategori_sumber_dana' => $communityService->kategori_sumber_dana,
                            'negara_sumber_dana' => $communityService->negara_sumber_dana,
                            'sumber_dana' => $communityService->sumber_dana,
                            'sumber_data' => $communityService->sumber_data,
                            'kd_program_hibah' => $communityService->kd_program_hibah,
                            'program_hibah' => $communityService->program_hibah,
                            'member' => $communityService->members->map(function ($member) {
                                return [
                                    'author_id' => $member->author_id,
                                    'nidn' => $member->nidn,
                                    'nama' => $member->name
                                ];
                            })
                        ];

                        $syncedDocs[] = $formattedDoc;
                        DB::commit();
                        \Log::info("Successfully synced document ID: {$doc['id']} with its members");
                    } catch (Exception $e) {
                        DB::rollBack();
                        \Log::error("Error syncing document ID {$doc['id']}: " . $e->getMessage());
                        throw $e;
                    }
                }
            }

            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Community Service Author',
                'status' => 'success'
            ]);

            return response()->json([
                "message" => "Synced doc community service author data from SINTA successfully",
                "status" => true,
                "data" => $syncedDocs
            ], 200);
        } catch (\Throwable $th) {
            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Community Service Author',
                'status' => 'failed',
            ]);

            return response()->json([
                "message" => "Failed to sync doc community service author data from SINTA",
                "status" => false,
                "error" => $th->getMessage()
            ], 500);
        }
    }

    public static function exportDataDocCommunityServiceAuthor($implementationYear){
        try {
            return Excel::download(new ExportDocCommunityServiceAuthor($implementationYear), 'doc_community_service_author_data_' . $implementationYear . '.xlsx');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export DocCommunityServiceAuthor data.'
            ], 500);
        }
    }

    public static function importDataDocCommunityServiceAuthor(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
    
        try {
            $importer = new ImportDocCommunityServiceAuthor();
            Excel::import($importer, $request->file('file'));
    
            $validationResults = $importer->validationResults;
    
            if (collect($validationResults)->contains(fn($result) => preg_match('/tidak sesuai/i', $result['status']))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data in the uploaded file.',
                    'errors' => $validationResults,
                ], 422);
            }
            return response()->json([
                'success' => true,
                'message' => 'DocCommunityserviceAuthor data imported successfully',
                'validationResults' => $validationResults,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import DocCommunityserviceAuthor data.',
                "error" => $th->getMessage()
            ], 500);
        }
    }
}

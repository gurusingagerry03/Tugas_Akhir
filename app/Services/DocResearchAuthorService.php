<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\DocResearchAuthor;
use App\Models\MemberResearch;
use App\Models\ProfileAuthor;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportDocResearchAuthor;
use App\Models\LogSyncSinta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Imports\ImportDocResearchAuthor;

class DocResearchAuthorService
{
    public static function transformDocResearchAuthor($doc)
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

    public static function getAllDocResearchAuthor()
    {
        try {
            $data = DocResearchAuthor::with('members')->get();

            $responseData = $data->map(function ($doc) {
                return self::transformDocResearchAuthor($doc);
            });

            return response()->json(
                [
                    'message' => 'DocResearchAuthor retrieved successfully',
                    'status' => true,
                    'data' => $responseData
                ]
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Failed to retrieve DocResearchAuthor',
                    'status' => false,
                    'error' => $e->getMessage(),
                ], 200
            );
        }
    }

    public static function getPaginationDocResearchAuthor (Request $request)
    {
        try {
            $facultyId = $request->query('faculty');
            $keyword = $request->query('keyword');
            $firstProposedYear = $request->query('first_proposed_year');
            $proposedYear = $request->query('proposed_year');
            $implementationYear = $request->query('implementation_year');
            $perPage = $request->query('items', 10);

            $docResearchAuthorQuery = DocResearchAuthor::query()
            ->when($facultyId, function ($query, $facultyId) {
                return $query->whereHas('profileAuthor.program', function ($q) use ($facultyId) {
                    $q->where('faculty_id', $facultyId);
                });
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%$keyword%")
                        ->orWhereHas('profileAuthor', function ($q) use ($keyword) {
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
            })
            ->orderBy('id', 'desc');

            $data = $docResearchAuthorQuery->with('members')->paginate($perPage);

            $transformedData = $data->getCollection()->map(function ($doc) {
                return self::transformDocResearchAuthor($doc);
            });

            $data->setCollection($transformedData);

            return response()->json([
                'message' => 'DocResearchAuthor retrieved successfully',
                'status' => true,
                'data' => $data,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to retrieve DocResearchAuthor',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getDocResearchAuthorById($id)
    {
        try {
            $data = DocResearchAuthor::with('members')->find($id);

            $transformedData = self::transformDocResearchAuthor($data);

            return response()->json([
                "message" => "Success to get doc research author by id",
                "status" => true,
                "data" => $transformedData
            ]);
        } catch (\Exception $th) {
            return response()->json([
                "message" => "Failed to get doc research author by id",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }

    public static function getDocResearchAuthorByAuthorId($author_id)
    {
        try {
            $data = DocResearchAuthor::with('members')->where('author_id', $author_id)->get();

            if($data->isEmpty()){
                return response()->json([
                    "message" => "Doc research author not found",
                    "status" => false
                ], 404);
            }

            $transformedData = $data->map(function ($doc) {
                return self::transformDocResearchAuthor($doc);
            });

            return response()->json([
                "message" => "Success to get doc research author by author id",
                "status" => true,
                "data" => $transformedData
            ]);
        } catch (\Exception $th) {
            return response()->json([
                "message" => "Failed to get doc research author by author id",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }

    public function updateDocResearchAuthor(Request $request,$id_research_doc){
        try {
            $validatedData = $request->validate([
                'author_id'                  => 'required|integer',
                'leader'                     => 'required|string',
                'leader_nidn'                => 'required|string',
                'title'                      => 'required|string',
                'first_proposed_year'        => 'required|integer',
                'proposed_year'              => 'required|integer',
                'implementation_year'        => 'required|integer',
                'focus'                      => 'required|string',
                'funds_approved'             => 'required|string',
                'scheme_name'                => 'required|string',
                'scheme_abbrev'              => 'required|string',
                'partner_leader_name'        => 'required|string',
                'partner_member1'            => 'required|string',
                'partner_member2'            => 'required|string' ,
                'partner_member3'            => 'required|string',
                'partner_member4'            => 'required|string',
                'student_thesis_title'       => 'required|string',
                'subject_title'              => 'required|string',
                'funds_total'                => 'required|string',
                'fund_category'              => 'required|string',
                'tkt'                        => 'required|string',
                'sdgs_id'                    => 'required|integer',
                'member'                     => 'required|array',
                'member.*.author_id'         => 'required|integer',
                'member.*.nidn'              => 'required|string',
                'member.*.name'              => 'required|string',
            ]);

            MemberResearch::where('research_id', $id_research_doc)->delete();

            foreach ($validatedData['member'] as $index => $member) {
                if (!empty($member['name'])) {
                    $memberResearchData = [
                        'research_id' => $id_research_doc,
                        'name' => $member['name'],
                        'author_id' => $member['author_id'] ?? 0,
                        'nidn' => $member['nidn'] ?? '',
                        'ordernum' => $index + 1
                    ];

                    MemberResearch::create($memberResearchData);
                }
            }
            $docResearchAuthor = DocResearchAuthor::with('members')->findOrFail($id_research_doc);
            $docResearchAuthor->update($validatedData);

            $transformedData = self::transformDocResearchAuthor($docResearchAuthor);

            return response()->json([
                'message' => 'DocResearchAuthor updated successfully',
                'status' => true,
                'data' => $transformedData
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'DocResearchAuthor Validation failed',
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update the DocResearchAuthor',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteDocResearchAuthor($id_research_doc)
    {
        try {
            $DocResearchAuthor = DocResearchAuthor::findOrFail($id_research_doc);
            $memberResearchData = MemberResearch::where('research_id', $id_research_doc);
            $DocResearchAuthor->delete();
            $memberResearchData->delete();
            return response()->json([
                'message' => 'DocResearchAuthor deleted successfully',
                'status' => true,
            ], 202);
        } catch (\Throwable $e){
            return response()->json([
                'message' => 'failed to delete the DocResearchAuthor',
                'status' => false,
                'error' => $e->getMessage()], 404);
        }
    }

    public function insertDocResearchAuthor(Request $request){
        try {
            $validatedData = $request->validate([
                'author_id'                  => 'required|integer',
                'leader'                     => 'required|string',
                'leader_nidn'                => 'required|string',
                'title'                      => 'required|string',
                'first_proposed_year'        => 'required|integer',
                'proposed_year'              => 'required|integer',
                'implementation_year'        => 'required|integer',
                'focus'                      => 'required|string',
                'funds_approved'             => 'required|string',
                'scheme_name'                => 'required|string',
                'scheme_abbrev'              => 'required|string',
                'partner_leader_name'        => 'required|string',
                'partner_member1'            => 'required|string',
                'partner_member2'            => 'required|string' ,
                'partner_member3'            => 'required|string',
                'partner_member4'            => 'required|string',
                'student_thesis_title'       => 'required|string',
                'subject_title'              => 'required|string',
                'funds_total'                => 'required|string',
                'fund_category'              => 'required|string',
                'tkt'                        => 'required|string',
                'sdgs_id'                    => 'required|integer',
                'member'                     => 'required|array',
                'member.*.author_id'         => 'required|integer',
                'member.*.nidn'              => 'required|string',
                'member.*.name'              => 'required|string',
            ]);

            $docResearchAuthor = DocResearchAuthor::create($validatedData);

            foreach ($validatedData['member'] as $index => $member) {
                if (!empty($member['name'])) {
                    $memberResearchData = [
                        'research_id' => $docResearchAuthor->id,
                        'name' => $member['name'],
                        'author_id' => $member['author_id'] ?? 0,
                        'nidn' => $member['nidn'] ?? '',
                        'ordernum' => $index + 1
                    ];

                    MemberResearch::create($memberResearchData);
                }
            }

            $transformedData = self::transformDocResearchAuthor($docResearchAuthor);

            return response()->json([
                'message' => 'DocResearchAuthor added successfully',
                'status' => true,
                'data' => $transformedData
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to add the DocResearchAuthor',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function syncFromSintaDocResearchAuthor()
    {
        try {
            $tokenSinta = LoginSintaService::loginSinta(new Request());
            $authors = ProfileAuthor::select('NIDN')->distinct()->get();

            foreach ($authors as $author) {
                $sintaApiUrl = "http://apisinta.kemdikbud.go.id/v3/dev/271071775/author/research/nidn/{$author->NIDN}";

                $response = Http::withToken($tokenSinta)->post($sintaApiUrl);


                $jsonData = json_decode($response->getBody(), true);
                $authorId = $jsonData['results']['authors']['author_id'];
                $documents = $jsonData['results']['documents'];

                foreach ($documents as $doc) {
                    DocResearchAuthor::updateOrCreate(
                        [
                            'id'                    => $doc['id'],
                            'author_id'             => $authorId,
                            'leader_nidn'           => $doc['leader_nidn'],
                            'title'                 => $doc['title'],
                        ],
                        [
                            'leader'                => !empty($doc['leader']) ? $doc['leader'] : '',
                            'first_proposed_year'   => !empty($doc['first_proposed_year']) ? $doc['first_proposed_year'] : 0,
                            'proposed_year'         => !empty($doc['proposed_year']) ? $doc['proposed_year'] : 0,
                            'implementation_year'   => !empty($doc['implementation_year']) ? $doc['implementation_year'] : 0,
                            'focus'                 => !empty($doc['focus']) ? $doc['focus'] : '',
                            'funds_approved'        => !empty($doc['funds_approved']) ? $doc['funds_approved'] : '',
                            'scheme_name'           => !empty($doc['scheme']['name']) ? $doc['scheme']['name'] : '',
                            'scheme_abbrev'         => !empty($doc['scheme']['abbrev']) ? $doc['scheme']['abbrev'] : '',
                        ]
                    );

                    if (!empty($doc['member'])) {
                        foreach ($doc['member'] as $index => $member) {
                            MemberResearch::updateOrCreate(
                                [
                                    'research_id' => $doc['id'],
                                    'author_id' => $member['author_id']
                                ],
                                [
                                    'nidn'      => $member['nidn'],
                                    'name'      => $member['nama'],
                                    'ordernum'    => $index + 1,
                                ]
                            );
                        }
                    }
                }
            }

            $data = DocResearchAuthor::with('members')->get();

            $responseData = $data->map(function ($doc) {
                return self::transformDocResearchAuthor($doc);
            });

            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Research Author',
                'status' => 'success'
            ]);

            return response()->json([
                "message" => "Synced doc research author data from SINTA successfully",
                "status" => true,
                "data" => $responseData
            ], 200);
        } catch (\Throwable $th) {
            LogSyncSinta::create([
                'username' => Auth::user()->username,
                'endpoint' => 'Doc Research Author',
                'status' => 'failed'
            ]);

            return response()->json([
                "message" => "Failed to sync doc research author data from SINTA",
                "status" => false,
                "error" => $th->getMessage()
            ], 500);
        }

    }

    public static function exportDataDocResearchAuthor($implementationYear){
        try {
            return Excel::download(new ExportDocResearchAuthor($implementationYear), 'doc_research_author_data_' . $implementationYear . '.xlsx');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export DocResearchAuthor data.'
            ], 500);
        }
    }

    public static function importDataDocResearchAuthor(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);
    
        try {
            $importer = new ImportDocResearchAuthor();
            Excel::import($importer, $request->file('file'));
    
            $validationResults = $importer->validationResults;
    
            if (collect($validationResults)->contains('status', 'Format tidak sesuai')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data in the uploaded file.',
                    'errors' => $validationResults,
                ], 422);
            }
            return response()->json([
                'success' => true,
                'message' => 'DocResearchAuthor data imported successfully',
                'validationResults' => $validationResults,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to import DocResearchAuthor data.',
                "error" => $th->getMessage()
            ], 500);
        }
    }
};

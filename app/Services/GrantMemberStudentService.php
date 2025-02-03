<?php

namespace App\Services;
use App\Models\DocResearchAuthor;
use App\Rules\GrantIdExists;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\GrantMemberStudent;
use Illuminate\Validation\ValidationException;

class GrantMemberStudentService
{
    public static function GetPaginationGrantMemberStudent(Request $request)
    {
        try {
            $keyword = $request->query('keyword');
            $items = $request->query('items', 10);

            $grantMemberStudentQuery = GrantMemberStudent::with(['research.members', 'communityService.members'])
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%")
                            ->orWhereHas('research.profileAuthor', function ($q) use ($keyword) {
                                $q->where('fullname', 'like', "%$keyword%");
                            })
                            ->orWhereHas('communityService.author', function ($q) use ($keyword) {
                                $q->where('fullname', 'like', "%$keyword%");
                            })
                            ->orWhereHas('research.members', function ($q) use ($keyword) {
                                $q->where('name', 'like', "%$keyword%");
                            })
                            ->orWhereHas('communityService.members', function ($q) use ($keyword) {
                                $q->where('name', 'like', "%$keyword%");
                            });
                        ;
                    });
                })
                ->orderBy('created_at', 'desc');

            $grantMemberStudents = $grantMemberStudentQuery->paginate($items);

            $grantMemberStudents->each(function ($grantMemberStudent) {

                if ($grantMemberStudent->research) {
                    unset($grantMemberStudent->communityService);
                } else {
                    unset($grantMemberStudent->research);
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'GrantMemberStudent retrieved successfully',
                'data' => $grantMemberStudents
            ], 200);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Failed to retrieve grantMemberStudent',
                    'status' => false,
                    'error' => $e->getMessage(),
                ],
                200
            );
        }
    }

    public static function getGrantMemberStudentById($id)
    {
        try {
            $grantMemberStudentQuery = GrantMemberStudent::where('id', $id)->firstOrFail();

            $grantMemberStudent = DocResearchAuthor::where('id', $grantMemberStudentQuery->grant_id)->exists()
                ? $grantMemberStudentQuery->load('research.members') :
                $grantMemberStudentQuery->load('communityService.members');

            return response()->json([
                'status' => true,
                'message' => 'grantMemberStudent with id ' . $id . ' retrieved successfully',
                'data' => $grantMemberStudent
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'grantMemberStudent with id ' . $id . ' not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve grantMemberStudent',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public static function insertGrantMemberStudent(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'grant_id' => ['required', 'integer', new GrantIdExists()],
                'student_id' => 'required|string',
            ]);

            $validated['grant_category_id'] = DB::table('doc_communityservice_author')
                ->where('id', $validated['grant_id'])
                ->exists() ? 2 : 1;

            $insertedGrantMemberStudent = GrantMemberStudent::create($validated);

            $grantMemberStudent = DocResearchAuthor::where('id', $insertedGrantMemberStudent->grant_id)->exists()
                ? $insertedGrantMemberStudent->load('research.members') :
                $insertedGrantMemberStudent->load('communityService.members');

            return response()->json([
                'status' => true,
                'message' => 'grantMemberStudent added successfully',
                'grantMemberStudent' => $grantMemberStudent
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation Failed'
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed to add the grantMemberStudent',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function updateGrantMemberStudent(Request $request, $id_GrantMemberStudent)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'grant_id' => ['required', 'integer', new GrantIdExists()],
                'student_id' => 'required|string',
            ]);

            $validated['grant_category_id'] = DB::table('doc_communityservice_author')
                ->where('id', $validated['grant_id'])
                ->exists() ? 2 : 1;

            $updatedGrantMemberStudent = GrantMemberStudent::findOrFail($id_GrantMemberStudent);
            $updatedGrantMemberStudent->update($validated);

            $grantMemberStudent = DocResearchAuthor::where('id', $updatedGrantMemberStudent->grant_id)->exists()
                ? $updatedGrantMemberStudent->load('research.members') :
                $updatedGrantMemberStudent->load('communityService.members');


            return response()->json([
                'message' => 'grantMemberStudent updated successfully',
                'status' => 'true',
                'data' => $grantMemberStudent
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation Failed'
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'failed to update the grantMemberStudent',
                'status' => 'false',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function deleteGrantMemberStudent($id)
    {
        try {
            $grantMemberStudent = GrantMemberStudent::findOrFail($id);
            $grantMemberStudent->delete();

            return response()->json([
                'status' => true,
                'message' => 'grantMemberStudent deleted successfully',
            ], 202);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed to delete the grantMemberStudent',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
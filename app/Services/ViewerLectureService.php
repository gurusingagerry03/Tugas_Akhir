<?php
namespace App\Services;
use App\Models\ProfileAuthor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\ViewerLecture;
use Illuminate\Validation\ValidationException;
use function Symfony\Component\Translation\t;

class ViewerLectureService{
    public static function getAllViewerService(Request $request): JsonResponse
    {
        try{
            $viewerLecture = ViewerLecture::all();

            return response()->json([
                'message' => 'Viewer lecture retrieved successfully',
                'status' => true,
                'data' => $viewerLecture
            ]);
        } catch (\Throwable $th){
            return response()->json([
                'message' => 'Failed to retrieve viewer lecture',
                'status' => false,
                'error' => $th->getMessage(),
            ], 200);
        } 
    }

    public static function createViewerLecture(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'author_id' => 'required|integer|exists:profile_author,id',
            ]);

            $validated['access_date'] = now();
            $viewerLecture = ViewerLecture::create($validated);

            return response()->json([
                'message' => 'Viewer lecture created successfully',
                'status' => true,
                'data' => $viewerLecture
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to create viewer lecture',
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public static function updateViewerLecture(Request $request, $id): JsonResponse
    {
        $validatedData = $request->validate([
            'author_id' => 'nullable|integer',
            'access_date' => 'nullable|date'
        ]);
    
        try {
            $viewerLecture = ViewerLecture::findOrFail($id);
    
            $viewerLecture->update($request->only(['author_id', 'access_date']));
    
            return response()->json([
                'message' => 'Viewer lecture updated successfully',
                'status' => true,
                'data' => $viewerLecture
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update viewer lecture',
                'status' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function deleteViewerLecture($id): JsonResponse
    {
        try {
            $viewerLecture = ViewerLecture::findOrFail($id);
            $viewerLecture->delete();
    
            return response()->json([
                'message' => 'Viewer lecture deleted successfully',
                'status' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to delete viewer lecture',
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function getTotalViewerByAuthor($author_id)
    {
        try {
            if ($author_id === null) {
                $totals = ProfileAuthor::leftJoin('viewer_lecture', 'profile_author.id', '=', 'viewer_lecture.author_id')
                ->select('profile_author.id as author_id', 'profile_author.fullname')
                ->selectRaw('COUNT(viewer_lecture.id) as total')
                ->groupBy('profile_author.id', 'profile_author.fullname')
                ->get()
                ->map(function ($item) {
                    return [
                        'author_id' => $item->author_id,
                        'fullname' => $item->fullname,
                        'total' => $item->total,
                    ];
                });

                return response()->json([
                    'message' => 'Total viewers by author retrieved successfully',
                    'status' => true,
                    'data' => $totals,
                ], 200);
            } else {
                $total = ViewerLecture::where('author_id', $author_id)->count();
                $fullname = ProfileAuthor::where('id', $author_id)->value('fullname');
                if (!$fullname) {
                    return response()->json([
                        'message' => 'Author dengan id ' . $author_id . ' tidak ada',
                        'status' => false,
                    ], 404);
                }
                return response()->json([
                    'message' => 'Total viewer author retrieved successfully',
                    'status' => true,
                    'data' => [
                        'author_id' => $author_id,
                        'fullname' => $fullname,
                        'total' => $total,
                    ],
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve total viewer lecture',
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}

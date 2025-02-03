<?php

namespace App\Services;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use League\CommonMark\Node\Block\Document;
use Illuminate\Support\Facades\Http;
use App\Models\ViewerPage;
use App\Models\WebPage;
use Illuminate\Validation\ValidationException;

class ViewerPageService
{
    public static function createViewerPage(Request $request)
    {
        try {
            $validated = $request->validate([
                'page_id' => 'required|integer|exists:web_page,id',
            ]);

            $validated['access_date'] = now();
            $newViewerPage = ViewerPage::create($validated);

            return response()->json([
                'message' => 'Viewer page created successfully',
                'status' => true,
                'data' => $newViewerPage
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to create viewer page',
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public static function readViewerPage(Request $request)
    {
        try {
            $read = ViewerPage::all();

            return response()->json([
                'message' => 'Viewer page retrieved successfully',
                'status' => true,
                'data' => $read
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve viewer page',
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public static function updateViewerPage(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'page_id' => 'nullable|integer',
                'access_date' => 'nullable',
            ]);

            $viewerPage = ViewerPage::find($id);

            if (!$viewerPage) {
                return response()->json([
                    'message' => 'Viewer page with the given ID not found',
                    'status' => false,
                ], 404);
            }

            $viewerPage->update([
                'page_id' => $validated['page_id'] ?? $viewerPage->page_id,
                'access_date' => $validated['access_date'] ?? $viewerPage->access_date,
            ]);

            return response()->json([
                'message' => 'Viewer page updated successfully',
                'status' => true,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'status' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to update viewer page',
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public static function deleteViewerPage($id)
    {
        try {
            $viewerPage = ViewerPage::findOrFail($id);
            $viewerPage->delete();

            return response()->json([
                'message' => 'Viewer page deleted successfully',
                'status' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to delete viewer page',
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

public function getTotalViewerByPage($page_id)
    {
        try {
            if ($page_id === null) {
                $totals = WebPage::leftJoin('viewer_page', 'web_page.id', '=', 'viewer_page.page_id')
                ->select('web_page.id as page_id', 'web_page.page_name')
                ->selectRaw('COUNT(viewer_page.id) as total')
                ->groupBy('web_page.id', 'web_page.page_name')
                ->get()
                ->map(function ($item) {
                    return [
                        'page_id' => $item->page_id,
                        'page_name' => $item->page_name,
                        'total' => $item->total,
                    ];
                });

                return response()->json([
                    'message' => 'Total viewers by page retrieved successfully',
                    'status' => true,
                    'data' => $totals,
                ], 200);
            } else {
                $total = ViewerPage::where('page_id', $page_id)->count();
                $pageName = WebPage::where('id', $page_id)->value('page_name');
                if (!$pageName) {
                    return response()->json([
                        'message' => 'Page dengan id ' . $page_id . ' tidak ada',
                        'status' => false,
                    ], 404);
                }
                return response()->json([
                    'message' => 'Total viewer pages retrieved successfully',
                    'status' => true,
                    'data' => [
                        'page_id' => $page_id,
                        'page_name' => $pageName,
                        'total' => $total,
                    ],
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to retrieve total viewer pages',
                'status' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}

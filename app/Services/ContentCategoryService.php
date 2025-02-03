<?php

namespace App\Services;

use App\Models\ContentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContentCategoryService
{
    public static function getAllContentCategories()
    {
        try {
            $contentCategories = ContentCategory::all();
            return response()->json([
                'status' => true,
                'message' => 'Content categories retrieved successfully',
                'data' => $contentCategories
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve content categories',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function getPaginateContentCategories()
    {
        try {
            $contentCategories = ContentCategory::withCount('articles')->paginate();

            $transformedCategories = $contentCategories->getCollection()->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->type,
                    'total_articles' => $category->articles_count,
                ];
            });

            $contentCategories->setCollection($transformedCategories);

            return response()->json([
                'status' => true,
                'message' => 'Content categories retrieved successfully',
                'data' => $contentCategories
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve content categories',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public static function showContentCategories($id)
    {
        try {
            $contentCategories = ContentCategory::withCount('articles')->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Content categories retrieved successfully',
                'data' => $contentCategories
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve content categories',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function createContentCategory(Request $request)
    {
        try {
            $data = new ContentCategory();
            $data->name = $request->name;
            $data->type = $request->type;
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'Content category created successfully',
                'data' => $data
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create content category',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function updateContentCategory(Request $request, $id)
    {
        try {
            $data = ContentCategory::findOrFail($id);
            $data->name = $request->name;
            $data->type = $request->type;
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'Content category updated successfully',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update content category',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public static function deleteContentCategory($id)
    {
        try {
            $data = ContentCategory::findOrFail($id);
            $data->delete();

            return response()->json([
                'status' => true,
                'message' => 'Content category deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete content category',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}

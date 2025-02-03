<?php

namespace App\Services;

use App\Models\Articles;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ArticleService
{
    public static function getAllArticle()
    {
        //
        try {
            $article = Articles::with(['users', 'categories'])
                ->orderBy('created_at', 'desc')
                ->get();
            $data = $article->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'input_date' => $article->input_date,
                    'status_publish' => $article->status_publish,
                    'news_content' => $article->news_content,
                    'thumbnail_image' => $article->thumbnail_image ? url('storage/articles/' . $article->thumbnail_image) : null,
                    'created_at' => $article->created_at,
                    'updated_at' => $article->updated_at,
                    'author' => [
                        'fullname' => $article->users ? $article->users->fullname : 'Unknown',
                    ],
                    'category' => $article->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    }),
                ];
            });

            return response()->json(
                [
                    'status' => true,
                    'message' => 'All articles found',
                    'data' => $data,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'failed to get All Article',
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    public function getArticleById(string $id)
    {
        try {
            $article = Articles::with(['users', 'categories'])->findOrFail($id);
            $data = [
                'id' => $article->id,
                'title' => $article->title,
                'input_date' => $article->input_date,
                'status_publish' => $article->status_publish,
                'news_content' => $article->news_content,
                'thumbnail_image' => $article->thumbnail_image ? url('storage/articles/' . $article->thumbnail_image) : null,
                'created_at' => $article->created_at,
                'updated_at' => $article->updated_at,
                'author' => [
                    'fullname' => $article->users ? $article->users->fullname : 'Unknown',
                ],
                'category' => $article->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                }),
            ];
            return response()->json(
                [
                    'status' => true,
                    'message' => 'article found',
                    'data' => $data,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'article not found',
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    public static function getPaginationArticle(Request $request)
    {
        try {
            $categoryId = $request->query('category_id');
            $keyword = $request->query('keyword');
            $items = $request->query('items', 10);

            $query = Articles::with(['categories', 'users'])
                ->when($categoryId, function ($q) use ($categoryId) {
                    $q->whereHas('categories', function ($q) use ($categoryId) {
                        $q->where('id', $categoryId);
                    });
                })
                ->when($keyword, function ($q) use ($keyword) {
                    $q->where(function ($query) use ($keyword) {
                        $query->where('title', 'like', '%' . $keyword . '%')->orWhere('news_content', 'like', '%' . $keyword . '%');
                    });
                })
                ->where('status_publish', 'publish')
                ->orderBy('created_at', 'desc');
            $articles = $query->paginate($items);
            $transformedArticles = $articles->getCollection()->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'input_date' => $article->input_date,
                    'status_publish' => $article->status_publish,
                    'news_content' => $article->news_content,
                    'thumbnail_image' => $article['thumbnail_image'] ? url('storage/articles/' . $article['thumbnail_image']) : null,
                    'created_at' => $article->created_at,
                    'updated_at' => $article->updated_at,
                    'author' => [
                        'fullname' => $article->users->fullname,
                    ],
                    'category' => $article->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    }),
                ];
            });
            $articles->setCollection($transformedArticles);

            return response()->json(
                [
                    'message' => 'Articles retrieved successfully',
                    'status' => true,
                    'data' => $articles,
                ],
                200,
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Failed to retrieve articles',
                    'status' => false,
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public static function getArticleAdmin(Request $request)
    {
        try {
            $categoryId = $request->query('category_id');
            $keyword = $request->query('keyword');
            $items = $request->query('items', 10);

            $query = Articles::with(['categories', 'users'])
                ->when($categoryId, function ($q) use ($categoryId) {
                    $q->whereHas('categories', function ($q) use ($categoryId) {
                        $q->where('id', $categoryId);
                    });
                })
                ->when($keyword, function ($q) use ($keyword) {
                    $q->where(function ($query) use ($keyword) {
                        $query->where('title', 'like', '%' . $keyword . '%')->orWhere('news_content', 'like', '%' . $keyword . '%');
                    });
                })
                ->orderBy('created_at', 'desc');
            $articles = $query->paginate($items);
            $transformedArticles = $articles->getCollection()->map(function ($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'input_date' => $article->input_date,
                    'status_publish' => $article->status_publish,
                    'news_content' => $article->news_content,
                    'thumbnail_image' => $article['thumbnail_image'] ? url('storage/articles/' . $article['thumbnail_image']) : null,
                    'created_at' => $article->created_at,
                    'updated_at' => $article->updated_at,
                    'author' => [
                        'fullname' => $article->users->fullname,
                    ],
                    'category' => $article->categories->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                        ];
                    }),
                ];
            });
            $articles->setCollection($transformedArticles);

            return response()->json(
                [
                    'message' => 'Articles retrieved successfully',
                    'status' => true,
                    'data' => $articles,
                ],
                200,
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Failed to retrieve articles',
                    'status' => false,
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public static function createArticle(Request $req)
    {
        try {
            $req->validate([
                'title' => 'required|string|max:255',
                'status_publish' => 'required|string|in:publish,draft,unpublish',
                'news_content' => 'required|string',
                'author' => 'required|integer|exists:users,id',
                'thumbnail_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'category_id' => 'required|array',
                'category_id.*' => 'integer|exists:content_category,id',
            ]);

            $article = new Articles();
            $article->title = $req->title;
            $article->input_date = Carbon::now();
            $article->status_publish = $req->status_publish;
            $article->news_content = $req->news_content;
            $article->author = $req->author;

            if ($req->hasFile('thumbnail_image')) {
                $path = $req->file('thumbnail_image')->store('public/articles/');
                $article->thumbnail_image = basename($path);
            }

            $article->save();
            $article->categories()->sync($req->category_id);
            $data = [
                'id' => $article->id,
                'title' => $article->title,
                'input_date' => $article->input_date,
                'status_publish' => $article->status_publish,
                'news_content' => $article->news_content,
                'thumbnail_image' => $article['thumbnail_image'] ? url('storage/articles/' . $article['thumbnail_image']) : null,
                'created_at' => $article->created_at,
                'updated_at' => $article->updated_at,
                'author' => [
                    'fullname' => $article->users ? $article->users->fullname : 'Unknown',
                ],
                'category' => $article->categories->map(function ($category) {
                    return [
                        'name' => $category->name,
                    ];
                }),
            ];
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Article successfully created',
                    'data' => $data,
                ],
                200,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Validation failed',
                    'error' => $e->validator->errors(),
                ],
                422,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Create article failed',
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    public static function updateArticle(Request $req, string $id)
    {
        try {
            $req->validate([
                'title' => 'sometimes|string|max:255',
                'status_publish' => 'sometimes|string|in:publish,draft,unpublish',
                'news_content' => 'sometimes|string',
                'author' => 'sometimes|int',
                'thumbnail_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'category_id' => 'sometimes|array',
                'category_id.*' => 'integer|exists:content_category,id',
            ]);

            $article = Articles::findOrFail($id);

            if ($req->has('title')) {
                $article->title = $req->title;
            }

            if ($req->has('status_publish')) {
                $article->status_publish = $req->status_publish;
            }

            if ($req->has('news_content')) {
                $article->news_content = $req->news_content;
            }

            if ($req->has('author')) {
                $article->author = $req->author;
            }

            if ($req->hasFile('thumbnail_image')) {
                if ($article->thumbnail_image) {
                    Storage::delete('public/articles/' . $article->thumbnail_image);
                }
                $path = $req->file('thumbnail_image')->store('public/articles/');
                $article->thumbnail_image = basename($path);
            }
            $article->input_date = Carbon::now();
            $article->save();

            if ($req->has('category_id')) {
                $article->categories()->sync($req->category_id);
            }

            $data = [
                'id' => $article->id,
                'title' => $article->title,
                'input_date' => $article->input_date,
                'status_publish' => $article->status_publish,
                'news_content' => $article->news_content,
                'thumbnail_image' => $article['thumbnail_image'] ? url('storage/articles/' . $article['thumbnail_image']) : null,
                'created_at' => $article->created_at,
                'updated_at' => $article->updated_at,
                'author' => [
                    'fullname' => $article->users ? $article->users->fullname : 'Unknown',
                ],
                'category' => $article->categories->map(function ($category) {
                    return [
                        'name' => $category->name,
                    ];
                }),
            ];

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Article successfully updated',
                    'data' => $data,
                ],
                200,
            );
        } catch (ValidationException $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Validation failed',
                    'error' => $e->validator->errors(),
                ],
                422,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Update article failed',
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    public static function deleteArticle(string $id)
    {
        try {
            $article = Articles::findOrFail($id);

            if ($article->thumbnail_image) {
                Storage::delete('public/articles/' . $article->thumbnail_image);
            }

            $article->categories()->detach();

            $article->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Article deleted successfully',
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Delete article failed',
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }
}

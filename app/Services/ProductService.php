<?php

namespace App\Services;

use App\Models\DocResearchAuthor;
use App\Rules\GrantIdExists;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ProductService
{

    public static function getAllProduct()
    {
        try {
            $product = Product::all();

            return response()->json([
                'message' => 'product retrieved successfully',
                'status' => 'true',
                'data' => $product
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to retrieve product',
                'status' => 'false',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public static function GetPaginationProduct(Request $request)
    {
        try {
            $keyword = $request->query('keyword');
            $tkt = $request->query('tkt');
            $year = $request->query('year');
            $items = $request->query('items', 10);

            $productQuery = Product::with(['research.members', 'communityService.members'])
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
                        });;
                });
            })
            ->when($tkt, function ($query) use ($tkt) {
                $query->where('tkt', 'like', '%' . $tkt . '%');
            })
            ->when($year, function ($query) use ($year) {
                $query->where('year', 'like', '%' . $year . '%');
            })
            ->orderBy('created_at', 'desc');


        $products = $productQuery->paginate($items);

        $products->each(function ($product) {

            if ($product->research) {
                unset($product->communityService);
            } else {
                unset($product->research);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Product retrieved successfully',
            'data' => $products
        ], 200);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Failed to retrieve product',
                    'status' => false,
                    'error' => $e->getMessage(),
                ],
                200
            );
        }
    }

    public static function getProductById($id)
    {
        try {
            $productQuery = Product::where('id', $id)->firstOrFail();

            $product = DocResearchAuthor::where('id', $productQuery->grant_id)->exists()
                ? $productQuery->load('research.members') :
                $productQuery->load('communityService.members');

            return response()->json([
                'status' => true,
                'message' => 'product with id ' . $id . ' retrieved successfully',
                'data' => $product
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'product with id ' . $id . ' not found',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function getProductByAuthorId($authorId)
    {
        try {
            $productResearch = Product::with(['research.members'])
                ->whereHas('research.members', function ($query) use ($authorId) {
                    $query->where('author_id', $authorId);
                })
                ->orWhereHas('research', function ($query) use ($authorId) {
                    $query->where('author_id', $authorId);
                });

            $productComService = Product::with(['communityService.members'])
                ->whereHas('communityService.members', function ($query) use ($authorId) {
                    $query->where('author_id', $authorId);
                })
                ->orWhereHas('communityService', function ($query) use ($authorId) {
                    $query->where('author_id', $authorId);
                });

            $product = $productResearch->union($productComService->getQuery())
                ->paginate();

            if ($product->isEmpty()) {
                throw new ModelNotFoundException("Product with author id: $authorId can't be found");
            }

            return response()->json([
                'status' => true,
                'message' => 'product retrieved successfully',
                'data' => $product
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Return null data',
                'error' => $e->getMessage()
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function insertProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'grant_id' => ['required', 'integer', new GrantIdExists()],
                'category' => 'required|string|in:product,prototype',
                'tkt' => 'required|string',
                'year' => 'required|digits:4|int',
                'description' => 'required|string',
                'cover' => 'required|url|string'
            ]);

            $validated['grant_category_id'] = DB::table('doc_communityservice_author')
                ->where('id', $validated['grant_id'])
                ->exists() ? 2 : 1;

            $insertedProduct = Product::create([
                'name' => $validated['name'],
                'grant_id' => $validated['grant_id'],
                'grant_category_id' => $validated['grant_category_id'],
                'category' => $validated['category'],
                'tkt' => $validated['tkt'],
                'year' => $validated['year'],
                'description' => $validated['description'],
                'cover' => $validated['cover']
            ]);

            $product = DocResearchAuthor::where('id', $insertedProduct->grant_id)->exists()
                ? $insertedProduct->load('research.members') :
                $insertedProduct->load('communityService.members');

            return response()->json([
                'status' => true,
                'message' => 'product added successfully',
                'product' => $product
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation Failed'
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed to add the product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

 public static function updateProduct(Request $request, $id_product)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'grant_id' => ['required', 'integer', new GrantIdExists()],
                'category' => 'required|string|in:product,prototype',
                'tkt' => 'required|string',
                'year' => 'required|digits:4|int',
                'description' => 'required|string',
                'cover' => 'required|url|string'
            ]);

            $validated['grant_category_id'] = DB::table('doc_communityservice_author')
                ->where('id', $validated['grant_id'])
                ->exists() ? 2 : 1;

            $updatedProduct = Product::findOrFail($id_product);
            $updatedProduct->update([
                'name' => $validated['name'],
                'grant_id' => $validated['grant_id'],
                'grant_category_id' => $validated['grant_category_id'],
                'category' => $validated['category'],
                'tkt' => $validated['tkt'],
                'year' => $validated['year'],
                'description' => $validated['description'],
                'cover' => $validated['cover']
            ]);

            $product = DocResearchAuthor::where('id', $updatedProduct->grant_id)->exists()
                ? $updatedProduct->load('research.members') :
                $updatedProduct->load('communityService.members');


            return response()->json([
                'message' => 'product updated successfully',
                'status' => 'true',
                'data' => $product
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
                'message' => 'Validation Failed'
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'failed to update the product',
                'status' => 'false',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public static function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'status' => true,
                'message' => 'product deleted successfully',
            ], 202);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'failed to delete the product',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}

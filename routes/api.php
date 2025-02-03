<?php

use App\Http\Controllers\Api\ArticleCategoryMappingController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\DocBookAuthorController;
use App\Http\Controllers\Api\DocCommunityserviceAuthorController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\ContentCategoryController;
use App\Http\Controllers\Api\DaftarAfiliasiController;
use App\Http\Controllers\Api\GrantMemberStudentController;
use App\Http\Controllers\Api\ProfileAuthorController;
use App\Http\Controllers\Api\ProfileProgramController;
use App\Http\Controllers\Api\DaftarJurnalController;
use App\Http\Controllers\Api\DocGarudaAuthorController;
use App\Http\Controllers\Api\DocGoogleAuthorController;
use App\Http\Controllers\Api\DocIprAuthorController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserPriviledgeController;
use App\Http\Controllers\Api\UserPriviledgeMappingController;
use App\Http\Controllers\Api\ViewerLectureController;
use App\Http\Controllers\Api\ViewerPageController;
use App\Http\Controllers\Api\GrantFundsEksternalController;
use App\Http\Controllers\Api\GrantSDGController;
use App\Http\Controllers\Api\LoginSintaController;
use App\Http\Controllers\Api\DocResearchAuthorController;
use App\Http\Controllers\Api\DocScopusAuthorController;
use App\Http\Controllers\Api\SDGController;
use App\Http\Controllers\Api\UserLogController;
use App\Http\Controllers\Api\WebPageController;
use App\Http\Controllers\Api\DocWosAuthorController;
use App\Http\Controllers\Api\GrantCollaboratorCategoryController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileFacultyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {

    // get /auth for profile, post /auth for login
    Route::prefix('auth')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->middleware('auth:sanctum');
        Route::post('/', [LoginController::class, 'login']);
    });

    Route::prefix('daftar-afiliasi')->group(function () {
        Route::get('/', [DaftarAfiliasiController::class, 'index']);
        Route::get('/paginate', [DaftarAfiliasiController::class, 'getPaginate']);
    });

    Route::prefix('profile-faculty')->group(function () {
        Route::get('/', [ProfileFacultyController::class, 'index']);
    });

    Route::prefix('daftar-jurnal')->group(function () {
        Route::get('/', [DaftarJurnalController::class, 'index']);
        Route::get('/paginate', [DaftarJurnalController::class, 'getPaginate']);
        Route::post('/', [DaftarJurnalController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/', [DaftarJurnalController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/', [DaftarJurnalController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('profile-program')->group(function () {
        Route::get('/paginate', [ProfileProgramController::class, 'getPaginate']);
        Route::get('/{id}', [ProfileProgramController::class, 'show']);
        Route::get('/', [ProfileProgramController::class, 'index']);
        Route::post('/', [ProfileProgramController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [ProfileProgramController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [ProfileProgramController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::post('/sync-from-sinta', [ProfileProgramController::class, 'syncFromSinta'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('profile-author')->group(function () {
        Route::get('/', [ProfileAuthorController::class, 'index']);
        Route::post('/sync-from-sinta', [ProfileAuthorController::class, 'syncFromSinta'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::get('/{id}', [ProfileAuthorController::class, 'show']);
        Route::post('/', [ProfileAuthorController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [ProfileAuthorController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [ProfileAuthorController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('doc-research-author')->group(function () {
        Route::get('/', [DocResearchAuthorController::class, 'index']);
        Route::get('/export/{implementation_year}', [DocResearchAuthorController::class, 'exportDataExcel']);
        Route::post('/import', [DocResearchAuthorController::class, 'importDataExcel']);
        Route::get('/{id}', [DocResearchAuthorController::class, 'getDocResearchAuthorById']);
        Route::get('/author/{id}', [DocResearchAuthorController::class, 'getDocResearchAuthorByAuthorId']);
        Route::post('/', [DocResearchAuthorController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [DocResearchAuthorController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [DocResearchAuthorController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::post('/sync-from-sinta', [DocResearchAuthorController::class, 'syncFromSinta'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('doc-book-author')->group(function () {
        Route::get('/', [DocBookAuthorController::class, 'index']);
        Route::get('/{id}', [DocBookAuthorController::class, 'show']);
        Route::get('/author/{authorId}', [DocBookAuthorController::class, 'showByAuthorId']);
        Route::post('/', [DocBookAuthorController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [DocBookAuthorController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [DocBookAuthorController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::post('/sync-from-sinta', [DocBookAuthorController::class, 'syncFromSinta'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('doc-communityservice-author')->group(function () {
        Route::get('/', [DocCommunityserviceAuthorController::class, 'index']);
        Route::get('/export/{implementation_year}', [DocCommunityserviceAuthorController::class, 'exportDataExcel']);
        Route::post('/import', [DocCommunityserviceAuthorController::class, 'importDataExcel']);
        Route::get('/{id}', [DocCommunityserviceAuthorController::class, 'show']);
        Route::get('/author/{authorId}', [DocCommunityserviceAuthorController::class, 'getByAuthorId']);
        Route::post('/', [DocCommunityserviceAuthorController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [DocCommunityserviceAuthorController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [DocCommunityserviceAuthorController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::post('/sync-from-sinta', [DocCommunityserviceAuthorController::class, 'sync'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('doc-ipr-author')->group(function () {
        Route::get('/', [DocIprAuthorController::class, 'index']);
        Route::get('/{id}', [DocIprAuthorController::class, 'show']);
        Route::get('/author/{authorId}', [DocIprAuthorController::class, 'showByAuthorId']);
        Route::post('/', [DocIprAuthorController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [DocIprAuthorController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [DocIprAuthorController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::post('/sync-from-sinta', [DocIprAuthorController::class, 'syncFromSinta'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('doc-scopus-author')->group(function () {
        Route::get('/', [DocScopusAuthorController::class, 'index']);
        Route::get('/{id}', [DocScopusAuthorController::class, 'show']);
        Route::get('/author/{authorId}', [DocScopusAuthorController::class, 'getByAuthorId']);
        Route::post('/', [DocScopusAuthorController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [DocScopusAuthorController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [DocScopusAuthorController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::post('/sync-from-sinta', [DocScopusAuthorController::class, 'sync'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('doc-wos-author')->group(function () {
        Route::get('/', [DocWosAuthorController::class, 'index']);
        Route::post('/', [DocWosAuthorController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [DocWosAuthorController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [DocWosAuthorController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::post('/sync-from-sinta', [DocWosAuthorController::class, 'syncFromSinta'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::get('/{id}', [DocWosAuthorController::class, 'getDocWosAuthorById']);
        Route::get('/author-id/{id}', [DocWosAuthorController::class, 'getDocWosAuthorByAuthorId']);
    });

    Route::prefix('doc-garuda-author')->group(function () {
        Route::get('/', [DocGarudaAuthorController::class, 'index']);
        Route::get('/{id}', [DocGarudaAuthorController::class, 'show']);
        Route::get('/author/{authorId}', [DocGarudaAuthorController::class, 'showByAuthorId']);
        Route::post('/', [DocGarudaAuthorController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [DocGarudaAuthorController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [DocGarudaAuthorController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::post('/sync-from-sinta', [DocGarudaAuthorController::class, 'syncFromSinta'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('doc-google-author')->group(function () {
        Route::get('/', [DocGoogleAuthorController::class, 'index']);
        Route::get('/{id}', [DocGoogleAuthorController::class, 'show']);
        Route::get('/author/{authorId}', [DocGoogleAuthorController::class, 'showByAuthorId']);
        Route::post('/', [DocGoogleAuthorController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [DocGoogleAuthorController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [DocGoogleAuthorController::class, 'delete'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::post('/sync-from-sinta', [DocGoogleAuthorController::class, 'syncFromSinta'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('grant-funds-eksternal')->group(function () {
        Route::get('/', [GrantFundsEksternalController::class, 'index']);
        Route::get('/paginate', [GrantFundsEksternalController::class, 'getPaginate']);
        Route::get('/{id}', [GrantFundsEksternalController::class, 'show']);
        Route::post('/', [GrantFundsEksternalController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [GrantFundsEksternalController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [GrantFundsEksternalController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('grant-collaborator-category')->group(function () {
        Route::get('/', [GrantCollaboratorCategoryController::class, 'index']);
        Route::get('/paginate', [GrantCollaboratorCategoryController::class, 'getPaginate']);
        Route::get('/{id}', [GrantCollaboratorCategoryController::class, 'show']);
        Route::post('/', [GrantCollaboratorCategoryController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [GrantCollaboratorCategoryController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [GrantCollaboratorCategoryController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('grant-sdg')->group(function () {
        Route::get('/', [GrantSDGController::class, 'getPaginate']);
        Route::get('/total-sdgs', [GrantSDGController::class, 'getTotalSdgs']);
        Route::get('/{id}', [GrantSDGController::class, 'show']);
        Route::post('/', [GrantSDGController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{grantId}', [GrantSDGController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{grantId}', [GrantSDGController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('sdg')->group(function () {
        Route::get('/', [SDGController::class, 'index']);
        Route::get('/paginate', [SDGController::class, 'getPaginate']);
        Route::get('/{id}', [SDGController::class, 'show']);
        Route::post('/', [SDGController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [SDGController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [SDGController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('sinta')->group(function () {
        Route::post('/login', [LoginSintaController::class, 'login']);
    });

    Route::prefix('content-category')->group(function () {
        Route::get('/', [ContentCategoryController::class, 'index']);
        Route::get('/{id}', [ContentCategoryController::class, 'show']);
        Route::post('/', [ContentCategoryController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [ContentCategoryController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [ContentCategoryController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('article')->group(function () {
        Route::get('/', [ArticleController::class, 'index']);
        Route::get('/admin', [ArticleController::class, 'getArticleAdmin'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::get('/{id}', [ArticleController::class, 'show']);
        Route::post('/', [ArticleController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [ArticleController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [ArticleController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('contact-us')->group(function () {
        Route::get('/', [ContactUsController::class, 'index']);
        Route::get('/{id}', [ContactUsController::class, 'show']);
        Route::post('/', [ContactUsController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [ContactUsController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [ContactUsController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('product')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/author/{authorId}', [ProductController::class, 'getProductByAuthorId']);
        Route::get('/{id}', [ProductController::class, 'show']);
        Route::post('/', [ProductController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [ProductController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [ProductController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('grant-member-student')->group(function () {
        Route::get('/', [GrantMemberStudentController::class, 'index']);
        Route::get('/{id}', [GrantMemberStudentController::class, 'show']);
        Route::post('/', [GrantMemberStudentController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [GrantMemberStudentController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [GrantMemberStudentController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('user-log')->group(function () {
        Route::get('/', [UserLogController::class, 'index']);
        Route::get('/{id}', [UserLogController::class, 'show']);
        Route::post('/', [UserLogController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [UserLogController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [UserLogController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}/role', [UserController::class, 'updateRole'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('user-priviledge')->group(function () {
        Route::get('/', [UserPriviledgeController::class, 'index']);
        Route::post('/', [UserPriviledgeController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [UserPriviledgeController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [UserPriviledgeController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('user-priviledge-mapping')->group(function () {
        Route::get('/', [UserPriviledgeMappingController::class, 'index']);
        Route::post('/', [UserPriviledgeMappingController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [UserPriviledgeMappingController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [UserPriviledgeMappingController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('web-page')->group(function () {
        Route::get('/', [WebPageController::class, 'index']);
        Route::post('/', [WebPageController::class, 'store'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::put('/{id}', [WebPageController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [WebPageController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('viewer-lecture')->group(function () {
        Route::get('/', [ViewerLectureController::class, 'index']);
        Route::get('/total-by-author/{author_id?}', [ViewerLectureController::class, 'getTotalViewerByAuthor']);
        Route::post('/', [ViewerLectureController::class, 'store']);
        Route::put('/{id}', [ViewerLectureController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [ViewerLectureController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

    Route::prefix('viewer-page')->group(function () {
        Route::get('/', [ViewerPageController::class, 'index']);
        Route::get('/total-by-page/{page_id?}', [ViewerPageController::class, 'getTotalViewerByPage']);
        Route::post('/', [ViewerPageController::class, 'store']);
        Route::put('/{id}', [ViewerPageController::class, 'update'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
        Route::delete('/{id}', [ViewerPageController::class, 'destroy'])->middleware(['auth:sanctum', 'auth.check:Administrator,Staff']);
    });

});

<?php

namespace App\Services;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use League\CommonMark\Node\Block\Document;
use Illuminate\Support\Facades\Http;
use App\Services\LoginSintaService;
use App\Models\daftar_jurnal;

class DaftarJurnalService
{
    // public static function getDaftarJurnal(Request $request)
    // {
    //     $http = new \GuzzleHttp\Client;
    //     $token = LoginSintaService::LoginSinta($request);
    //     // $data = ScopusDoc::all();
    //     // return $token;die;
    //     $baseUrl     = config('app.guzzle_test_url').'/v3/';
    //     $author = 'author';
    //     $endpoint = 'journals';

    //     $env = 'dev';
    //     $uniq = '271071775';
    //     // $type = 'nidn';
    //     // $id = '0414098606';

    //     $url = "{$baseUrl}{$env}/{$uniq}/{$endpoint}";

    //     $users = $http->request('POST', $url, [
    // 		'headers' => [
    // 			'Authorization' => 	'Bearer '.$token
    // 		]
    // 	]);

    // 	//Untuk mendapatkan kode PP
    // 	$data = json_decode($users->getBody(), true);
    //     $data = $data['results']['journals'];
    //     return $data;die;

    //     foreach ($data as $full) {
    //         $create = daftar_jurnal::updateorcreate(
    //         [
    //             'id_master' => $full['id'] ?? NULL,
    //             'accreditation' => $full['accreditation'] ?? NULL,
    //             'eissn' => $full['eissn'] ?? NULL,
    //             'pissn' => $full['pissn'] ?? NULL,
    //             'issn' => $full['issn'] ?? NULL,
    //             'title' => $full['title'] ?? NULL,
    //             'institution' => $full['institution'] ?? NULL,
    //             'publisher' => $full['publisher'] ?? NULL,
    //             'url_Journal' => $full['url_journal'] ?? NULL,
    //             'url_Contact' => $full['url_contact'] ?? NULL,
    //             'url_Editor' => $full['url_editor'] ?? NULL,
    //             'impact_3y' => $full['impact_3y'] ?? NULL
    //         ]);
    //     }

    //     return $data;

    // }

    public static function getDaftarAuthor()
    {
        try {
            $author = daftar_jurnal::all();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Daftar Author Berhasil',
                    'data' => $author,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error',
                    'data' => $th,
                ],
                500,
            );
        }
    }

    public static function getPaginateDaftarJurnal()
    {
        try {
            $author = daftar_jurnal::with('daftar_afiliasi')->paginate();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Daftar Author Paginate Berhasil',
                    'data' => $author,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error',
                    'data' => $th,
                ],
                500,
            );
        }
    }

    public function createJournal(Request $request)
    {
        $validatedData = $request->validate([
            'id_master' => 'required|integer',
            'accreditation' => 'required|string|max:255',
            'eissn' => 'required|string|max:255',
            'issn' => 'required|string|max:255',
            'pissn' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'url_Journal' => 'required|url|max:255',
            'url_Contact' => 'required|url|max:255',
            'url_Editor' => 'required|url|max:255',
            'impact_3y' => 'required|numeric',
        ]);

        try {
            $journal = daftar_jurnal::create($validatedData);
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Journal created successfully',
                    'data' => $journal,
                ],
                201,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error creating journal',
                    'data' => $th,
                ],
                500,
            );
        }
    }

    public function updateJournal(Request $request, $id)
    {
        $validatedData = $request->validate([
            'id_master' => 'required|integer',
            'accreditation' => 'required|string|max:255',
            'eissn' => 'required|string|max:255',
            'issn' => 'required|string|max:255',
            'pissn' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'url_Journal' => 'required|url|max:255',
            'url_Contact' => 'required|url|max:255',
            'url_Editor' => 'required|url|max:255',
            'impact_3y' => 'required|numeric',
        ]);

        try {
            $journal = daftar_jurnal::findOrFail($id);
            $journal->update($validatedData);
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Journal updated successfully',
                    'data' => $journal,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error updating journal',
                    'data' => $th,
                ],
                500,
            );
        }
    }

    public function deleteJournal($id)
    {
        try {
            $journal = daftar_jurnal::findOrFail($id);
            $journal->delete();
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Journal deleted successfully',
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Error deleting journal',
                    'data' => $th,
                ],
                500,
            );
        }
    }
}

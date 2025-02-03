<?php

namespace App\Services;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use League\CommonMark\Node\Block\Document;
use Illuminate\Support\Facades\Http;
use App\Services\loginSintaService;
use App\Models\daftar_afiliasi;
use Exception;

class DaftarAfiliasiService
{
    //public static function getDaftarAfiliasi(Request $request)
    //{
    //    $http = new \GuzzleHttp\Client;
    //    $token = loginSintaService::loginSinta($request);
    //    // $data = ScopusDoc::all();
    // return $token;die;
    //    $baseUrl     = config('app.guzzle_test_url') . '/v3/';
    //    $endpoint = "affiliations";

    //   $env = 'dev';
    //   $uniq = '271071775';

    //   $url = "{$baseUrl}{$env}/{$uniq}/{$endpoint}";

    //  $users = $http->request('POST', $url, [
    //      'headers' => [
    //          'Authorization' =>     'Bearer ' . $token
    //      ]
    //  ]);

    //Untuk mendapatkan kode PP
    //  $data = json_decode($users->getBody(), true);
    // $affiliations = $data['results']['affiliations'];

    // return data;
    // die;

    // foreach ($affiliations as $full) {
    //     $create = daftar_afiliasi::create([
    //         'id_Afiliasi' => $full['id'] ?? NULL,
    //         'id_master' => $full['id'] ?? NULL,
    //         'code_Pddikti' => $full['id'] ?? NULL,
    //         'name' => $full['id'] ?? NULL,
    //         'abbreviaton' => $full['id'] ?? NULL,
    //         'country' => $full['id'] ?? NULL,
    //         'korwil_Scope' => $full['id'] ?? NULL,
    //         'lidikti_Scope' => $full['id'] ?? NULL,
    //         'website' => $full['id'] ?? NULL,
    //         'description' => $full['id'] ?? NULL,
    //         'sinta_score_v2_overall' => $full['id'] ?? NULL,
    //         'sinta_score_v2_3year' => $full['id'] ?? NULL,
    //         'sinta_score_v2_productivity_overall' => $full['id'] ?? NULL,
    //         'sinta_score_v2_productivity_3year' => $full['id'] ?? NULL,
    //         'sinta_score_v3_overall' => $full['id'] ?? NULL,
    //         'sinta_score_v3_3year' => $full['id'] ?? NULL,
    //         'sinta_score_v3_productivity_overall' => $full['id'] ?? NULL,
    //         'sinta_score_v3_productivity_3year' => $full['id'] ?? NULL
    //     ]);
    //   }
    // }

    public static function getAllDaftarAfiliasi(Request $req)
    {
        try {
            $data = daftar_afiliasi::all();
            return response()->json([
                "message" => "Success to get all data",
                "status" => true,
                "data" => $data
            ]);
        } catch (Exception $th) {
            return response()->json([
                "message" => "Failed to get all data",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }

    public static function getPaginateDaftarAfiliasi(Request $req)
    {
        try{
            $data = daftar_afiliasi::paginate();
            return response()->json([
                "message" => "success tp get all daftar afiliasi paginate",
                "status" => true,
                "data" => $data
            ]);
        }catch (Exception $th){
            return response()->json([
                "message" => "Failed to get all daftar_afiliasi",
                "status" => false,
                "error" => $th->getMessage()
            ]);
        }
    }
}


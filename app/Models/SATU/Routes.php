<?php

namespace App\Models\SATU;

use Illuminate\Database\Eloquent\Model;

class Routes extends Model
{
    protected $table = 'urls';
    protected $primaryKey = 'id';
    protected $connection = 'framework';

    public static function GetRoute($method)
    {
        $route = Routes::select(
                                    'urls.id as url_id',
                                    'urls.name as url_name',
                                    'urls.masked_name as url_masked_name',
                                    'urls.methods as url_method',
                                    'urls.parameters as url_parameter',
                                    'urls.name as function_name',
                                    'controllers.namespaces as controller_namespaces',
                                    'urls.scope_id as scope_name'
                                ) 
            ->leftJoin('controllers','controllers.id','=','urls.controller_id')
            ->leftJoin('applications', 'applications.id', '=', 'controllers.application_id')
            ->leftJoin('owners', 'owners.id', '=', 'applications.owner_id')
            ->where('applications.id', '=', env('APP_ID'))
            ->where('urls.methods', '=', $method)
            ->orderBy('urls.id', 'asc')            
            ->distinct()
            ->get()
            ->toArray();

        return $route;
    }
}

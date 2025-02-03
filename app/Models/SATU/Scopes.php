<?php

namespace App\Models\SATU;

use Illuminate\Database\Eloquent\Model;

class Scopes extends Model
{
    protected $connection = 'framework';
    protected $table = 'oauth_scopes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'updated_by',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPriviledge extends Model
{
    use HasFactory;

    // Tabel User_Priviledge
    // protected $connection =

    protected $table = 'user_priviledge';
    protected $primaryKey = 'id';
    public $timestamps = false;
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';
    // protected $primaryKey = 'id_author';
    // public $incrementing = false; 

    protected $fillable = [
        'name',
    ];
}

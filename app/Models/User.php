<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'author_id',
        'username',
        'password',
        'fullname',
        'email',
        'created_at',
        'updated_at',
    ];

    public function author()
    {
        return $this->belongsTo(ProfileAuthor::class, 'id_author');
    }

    public function user_priviledge()
    {
        return $this->belongsToMany(UserPriviledge::class, 'user_priviledge_mapping', 'user_id', 'priviledge_id');
    }

}

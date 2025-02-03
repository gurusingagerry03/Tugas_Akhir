<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPriviledgeMapping extends Model
{
    use HasFactory;

    protected $table = 'user_priviledge_mapping';

    protected $fillable = [
        'user_id',
        'priviledge_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function priviledge()
    {
        return $this->belongsTo(UserPriviledge::class, 'priviledge_id');
    }
}

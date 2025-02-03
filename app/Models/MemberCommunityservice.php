<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberCommunityservice extends Model
{
    protected $table = 'member_communityservice';
    
    protected $fillable = [
        'communityservice_id',
        'author_id',
        'nidn',
        'name',
        'ordernum'
    ];

    public function communityService()
    {
        return $this->belongsTo(DocCommunityserviceAuthor::class, 'communityservice_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    }
}
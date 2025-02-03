<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocCommunityServiceAuthor extends Model
{
    use HasFactory;

    protected $table = 'doc_communityservice_author';
    public $timestamps = true;
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id',
        'author_id',
        'leader',
        'leader_nidn',
        'title',
        'first_proposed_year',
        'proposed_year',
        'implementation_year',
        'focus',
        'funds_approved',
        'scheme_name',
        'scheme_abbrev',
        'tkt',
        'result_comservice',
        'target_society_name',
        'target_society_address',
        'target_society_cityorregency'
    ];

    public function author()
    {
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    }

    public function members()
    {
        return $this->hasMany(MemberCommunityservice::class, 'communityservice_id', 'id');
    }
    
    public function grantSdgs()
    {
        return $this->hasMany(GrantSDG::class, 'grant_id', 'id');
    }
}

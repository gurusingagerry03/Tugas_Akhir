<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocResearchAuthor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id';
    protected $table = 'doc_research_author';
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
        'partner_leader_name',
        'partner_member1',
        'partner_member2',
        'partner_member3',
        'partner_member4',
        'student_thesis_title',
        'subject_title',
        'funds_total',
        'fund_category',
        'tkt',
        'sdgs_id'
    ];

    public function profileAuthor()
    {
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    }

    public function members()
    {
        return $this->hasMany(MemberResearch::class, 'research_id', 'id');
    }

    public function products(){
        return $this->hasMany(Product::class, 'grant_id', 'id');
    }

    public function grantSdgs()
    {
        return $this->hasMany(GrantSDG::class, 'grant_id', 'id');
    }
}

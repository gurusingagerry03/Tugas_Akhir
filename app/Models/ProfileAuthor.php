<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileAuthor extends Model
{
    use HasFactory;

    protected $table ='profile_author';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'programs_id',
        'affiliation_id',
        'nidn',
        'fullname',
        'country',
        'academic_grade_raw',
        'academic_grade',
        'gelar_depan',
        'gelar_belakang',
        'last_education',
        'sinta_score_v2_overall',
        'sinta_score_v2_3year',
        'sinta_score_v3_overall',
        'sinta_score_v3_3year',
        'affiliation_score_v3_overall',
        'affiliation_score_v3_3year',
        'image',
    ];


    public function affiliation()
    {
        return $this->belongsTo(Affiliation::class, "affiliation_id")->select("id", "code_pddikti", "name");
    }

    public function program()
    {
        return $this->belongsTo(ProfileProgram::class, "programs_id", "code_pddikti")->select("code_pddikti", "faculty_id", "level", "name_id", "name_en");
    }


    public function docScopusAuthors()
    {
        return $this->hasMany(DocScopusAuthor::class, 'author_id');
    }

    public function docWosAuthors()
    {
        return $this->hasMany(DocWosAuthor::class, "author_id");
    }

    public function docGarudaAuthors()
    {
        return $this->hasMany(DocGarudaAuthor::class, "author_id");
    }

    public function docGoogleAuthors()
    {
        return $this->hasMany(DocGoogleAuthor::class, "author_id");
    }

    public function scopusAuthors()
    {
        return $this->hasOne(ScopusAuthor::class, 'author_id');
    }

    public function wosAuthors()
    {
        return $this->hasOne(WosAuthor::class, "author_id");
    }

    public function garudaAuthors()
    {
        return $this->hasOne(GarudaAuthor::class, "author_id");
    }

    public function googleAuthors()
    {
        return $this->hasOne(GoogleAuthor::class, "author_id");
    }

    public function communityServices()
    {
        return $this->hasMany(DocCommunityserviceAuthor::class, 'author_id', 'id');
    }

    public function docResearchAuthors()
    {
        return $this->hasMany(DocResearchAuthor::class, 'author_id', 'id');
    }

    public function iprs(){
        return $this->hasMany(DocIprAuthor::class, 'author_id', 'id');
    }

    public function books() {
        return $this->hasMany(DocBookAuthor::class, 'author_id', 'id');
    }
    
    public function profileProgram()
    {
        return $this->belongsTo(ProfileProgram::class, 'programs_id', 'code_pddikti');
    }
}

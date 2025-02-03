<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Affiliation;

class ProfileProgram extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id';
    protected $table = 'profile_programs';
    protected $fillable = [
        'id',
        'faculty_id',
        'code_pddikti',
        'name_id',
        'name_en',
        'level',
        'website',
        'affiliation_id',
        'sinta_score_v3_overall',
        'sinta_score_v3_3year',
        'sinta_score_v3_productivity_overall',
        'sinta_score_v3_productivity_3year',
    ];

    public function affiliation(): BelongsTo
    {
        return $this->belongsTo(Affiliation::class, 'affiliation_id', 'id');
    }
    public function faculty()
    {
        return $this->belongsTo(ProfileFaculty::class, 'faculty_id', 'id');
    }

    public function profileAuthors()
    {
        return $this->hasMany(ProfileAuthor::class, 'programs_id', 'code_pddikti');
    }

}

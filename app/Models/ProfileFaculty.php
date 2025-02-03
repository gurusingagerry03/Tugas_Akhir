<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileFaculty extends Model
{
    use HasFactory;

    protected $table = 'profile_faculty';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name_id',
        'name_en',
        'website'
    ];

    public function programs()
    {
        return $this->hasMany(ProfileProgram::class, 'faculty_id', 'id');
    }
}

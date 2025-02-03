<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrantMemberStudent extends Model
{
    use HasFactory;

    protected $table = 'grant_member_student';

    protected $primaryKey='id';

    protected $fillable= ['name', 'grant_id', 'grant_category_id' , 'student_id'];

    public function research(){
        return $this->hasOne(DocResearchAuthor::class, 'id', 'grant_id');
    }

    public function communityService(){
        return $this->hasOne(DocCommunityserviceAuthor::class, 'id', 'grant_id');
    }
}

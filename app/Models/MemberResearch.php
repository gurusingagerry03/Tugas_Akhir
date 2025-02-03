<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberResearch extends Model
{
    use HasFactory;
    protected $primaryKey = 'research_id';
    protected $table = 'member_research';
    protected $fillable = [
        'research_id',
        'author_id',
        'nidn',
        'name',
        'ordernum'
    ];

    public function docResearchAuthor()
    {
        return $this->belongsTo(DocResearchAuthor::class, 'research_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocGoogleAuthor extends Model
{
    use HasFactory;

    protected $table = 'doc_google_author';
    public $timestamps = true;
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'author_id',
        'title',
        'abstract',
        'authors',
        'journal_name',
        'publish_year',
        'citation',
        'url',
    ];

    public function profileAuthor()
    {
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    }

}

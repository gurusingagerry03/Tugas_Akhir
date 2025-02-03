<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocWosAuthor extends Model
{
    use HasFactory;

    protected $table = 'doc_wos_author';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'author_id',
        'publons_id',
        'wos_id',
        'doi',
        'title',
        'first_author',
        'last_author',
        'authors',
        'publish_date',
        'journal_name',
        'citation',
        'abstract',
        'publish_type',
        'publish_year',
        'page_begin',
        'page_end',
        'issn',
        'eissn',
        'url'
    ];

    public function profileAuthor()
    {
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    }
}

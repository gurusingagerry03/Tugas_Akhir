<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScopusAuthor extends Model
{
    use HasFactory;

    protected $table = "scopus_author";
    protected $primaryKey = 'author_id';

    protected $fillable = [
        'author_id',
        'total_document',
        'total_citation',
        'total_cited_doc',
        'h_index',
        'i10_index',
        'g_index',
        'g_index_3year'
    ];
}

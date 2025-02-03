<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WosAuthor extends Model
{
    use HasFactory;

    protected $table = "wos_author";
    protected $primaryKey = 'author_id';

    protected $fillable = [
        'author_id',
        'total_document',
        'total_citation',
        'total_cited_doc',
        'h_index'
    ];
}

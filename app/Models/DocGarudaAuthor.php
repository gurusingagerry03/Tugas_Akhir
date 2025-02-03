<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class DocGarudaAuthor extends Model
{
    use HasFactory;

    protected $table ='doc_garuda_author';
    public $timestamps = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'author_id',
        'author_order',
        'accreditation',
        'title',
        'abstract',
        'publisher_name',
        'publish_date',
        'publish_year',
        'doi',
        'citation',
        'source',
        'source_issue',
        'source_page',
        'url'
    ];

    public function profileAuthor()
    {
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    } 
    
}

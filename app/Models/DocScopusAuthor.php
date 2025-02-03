<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SintaDaftarAuthor;

class DocScopusAuthor extends Model
{
    use HasFactory;

    //Tabel Scopus
    // protected $connection = 'dashboard_inovasi';
    protected $table = 'doc_scopus_author';
    public $timestamps = true;
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'author_id',
        'quartile',
        'title',
        'publication_name',
        'creator',
        'page',
        'issn',
        'volume',
        'cover_date',
        'cover_display_date',
        'doi',
        'citedby_count',
        'aggregation_type',
        'url'
    ];


    public function author(){
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    }
}
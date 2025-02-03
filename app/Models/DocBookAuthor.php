<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocBookAuthor extends Model
{
    use HasFactory;

    protected $table ='doc_book_author';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'author_id',
        'category',
        'isbn',
        'title',
        'authors',
        'place',
        'publisher',
        'year'
    ];

    public function profileAuthor()
    {
        return $this->belongsTo(ProfileAuthor::class, 'author_id', 'id');
    }

}

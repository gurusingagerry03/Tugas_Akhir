<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Sinta_Daftar_Author;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id';
    protected $table = 'products';
    protected $fillable = [
        'grant_id',
        'grant_category_id',
        'category',
        'tkt',
        'year',
        'description',
        'cover',
        'name'
    ];

    public function research(){
        return $this->hasOne(DocResearchAuthor::class, 'id', 'grant_id');
    }

    public function communityService(){
        return $this->hasOne(DocCommunityserviceAuthor::class, 'id', 'grant_id');
    }

}

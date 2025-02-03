<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrantCollaboratorCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'grant_collaborator_category';

    protected $fillable = [
        'category_name',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SDG extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'sdgs';

    protected $fillable = [
        'name',
        'total_penelitian',
        'resources',
        'resources_group'
    ];

    public function grantSdgs()
    {
        return $this->hasMany(GrantSDG::class, 'sdgs_id', 'id');
    }
}

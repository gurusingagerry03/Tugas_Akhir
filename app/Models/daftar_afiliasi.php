<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class daftar_afiliasi extends Model
{
    use HasFactory;

    //    protected $connection = 'dashboard_inovasi';
    protected $table = 'daftar_afiliasi';
    public $timestamps = false;
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'id_master';
    public $incrementing = false;


    protected $fillable = [
        'id_Afiliasi',
        'id_master',
        'code_Pddikti',
        'name',
        'abbreviaton',
        'country',
        'korwil_Scope',
        'lidikti_Scope',
        'website',
        'description',
        'sinta_score_v2_overall',
        'sinta_score_v2_3year',
        'sinta_score_v2_productivity_overall',
        'sinta_score_v2_productivity_3year',
        'sinta_score_v3_overall',
        'sinta_score_v3_3year',
        'sinta_score_v3_productivity_overall',
        'sinta_score_v3_productivity_3year'
    ];
}

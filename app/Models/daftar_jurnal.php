<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class daftar_jurnal extends Model
{
    use HasFactory;

    // protected $connection = 'dashboard_inovasi'; protected $table = 'daftar_jurnal';
    protected $table = "daftar_jurnal";
    protected $primaryKey = 'id_jurnal';
    public $incrementing = false;

    protected $fillable =
    [
        'id_master',
        'accreditation',
        'eissn',
        'issn',
        'pissn',
        'title',
        'institution',
        'publisher',
        'url_Journal',
        'url_Contact',
        'url_Editor',
        'impact_3y'
    ];

    public function daftar_afiliasi(): BelongsTo
    {
        return $this->belongsTo(daftar_afiliasi::class, 'id_master', 'id_afiliasi');
    }
}

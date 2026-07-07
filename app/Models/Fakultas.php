<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fakultas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fakultas';

    protected $fillable = [
        'id_univs',
        'kode_fakultas',
        'nama_fakultas',
        'short_name',
    ];

    public function univ(): BelongsTo
    {
        return $this->belongsTo(Univ::class, 'id_univs');
    }

    public function prodis(): HasMany
    {
        return $this->hasMany(Prodi::class, 'fakultas_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Univ extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'univs';

    protected $fillable = [
        'kode_univ',
        'nama_univ',
        'address',
        'email',
        'website',
    ];

    public function fakultas(): HasMany
    {
        return $this->hasMany(Fakultas::class, 'id_univs');
    }
}

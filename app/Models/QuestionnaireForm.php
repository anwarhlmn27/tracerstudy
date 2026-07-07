<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionnaireForm extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'questionnaire_forms';

    protected $fillable = [
        'title',
        'target_role',
        'angkatan',
        'form_group',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(FormQuestion::class, 'form_id')->orderBy('sort_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FormResponse::class, 'form_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormQuestion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'form_id',
        'question_text',
        'question_description',
        'question_type',
        'db_source',
        'is_required',
        'sort_order',
        'section_id',
        'section_title',
        'has_others',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'has_others' => 'boolean',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireForm::class, 'form_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(FormQuestionOption::class, 'question_id')->orderBy('sort_order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FormResponseAnswer::class, 'question_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormResponse extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'form_id',
        'user_id',
        'evaluated_student_id',
        'guest_student_id',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireForm::class, 'form_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(FormResponseAnswer::class, 'response_id');
    }

    public function evaluatedStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'evaluated_student_id');
    }

    public function guestStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'guest_student_id');
    }
}

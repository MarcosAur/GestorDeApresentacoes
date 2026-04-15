<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationCriterion extends Model
{
    /** @use HasFactory<\Database\Factories\EvaluationCriterionFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'evaluation_criteria';

    protected $fillable = [
        'contest_id',
        'name',
        'max_score',
        'weight',
        'tiebreak_priority',
    ];

    /**
     * Get the contest that owns the criterion.
     */
    public function contest(): BelongsTo
    {
        return $this->belongsTo(Contest::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'status',
        'current_presentation_id',
    ];

    /**
     * Get the event that owns the contest.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the jurors for the contest.
     */
    public function jurors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'contest_jurors');
    }

    /**
     * Get the evaluation criteria for the contest.
     */
    public function evaluationCriteria(): HasMany
    {
        return $this->hasMany(EvaluationCriterion::class);
    }

    /**
     * Get the presentations for the contest.
     */
    public function presentations(): HasMany
    {
        return $this->hasMany(Presentation::class);
    }
}

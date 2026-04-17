<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PresentationScore extends Model
{
    /** @use HasFactory<\Database\Factories\PresentationScoreFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'presentation_id',
        'juror_id',
        'criterion_id',
        'assigned_score',
    ];

    public function presentation()
    {
        return $this->belongsTo(Presentation::class);
    }

    public function juror()
    {
        return $this->belongsTo(User::class, 'juror_id');
    }

    public function criterion()
    {
        return $this->belongsTo(EvaluationCriterion::class, 'criterion_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresentationDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'presentation_id',
        'type',
        'file_path',
    ];

    /**
     * Get the presentation that owns the document.
     */
    public function presentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presentation extends Model
{
    /** @use HasFactory<\Database\Factories\PresentationFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contest_id',
        'competitor_id',
        'work_title',
        'status',
        'justification_inapto',
        'qr_code_hash',
        'checkin_realizado',
        'presentation_order',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function competitor()
    {
        return $this->belongsTo(User::class, 'competitor_id');
    }
}

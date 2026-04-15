<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presentation extends Model
{
    /** @use HasFactory<\Database\Factories\PresentationFactory> */
    use HasFactory, SoftDeletes;
}

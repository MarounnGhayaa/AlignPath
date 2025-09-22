<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    /** @use HasFactory<\Database\Factories\ProblemFactory> */
    use HasFactory;

    protected $fillable = [
        'path_id',
        'title',
        'subtitle',
        'question',
        'first_answer',
        'second_answer',
        'third_answer',
        'correct_answer',
        'points',
    ];
}

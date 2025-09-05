<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    /** @use HasFactory<\Database\Factories\QuestFactory> */
    use HasFactory;

    protected $fillable = [
        'path_id',
        'title',
        'subtitle',
        'difficulty',
        'duration'
    ];
}

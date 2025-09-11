<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyConversationAnalysis extends Model {
    protected $fillable = [
        'user_id', 'thread_id', 'day', 'summary', 'attributes', 'raw'
    ];

    protected $casts = [
        'day' => 'date',
        'attributes' => 'array',
        'raw' => 'array',
    ];
}

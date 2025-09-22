<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatThread extends Model {
    protected $fillable = [
        'user_id', 'title', 'metadata', 'started_at', 'last_message_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany {
        return $this->hasMany(ChatMessage::class, 'thread_id');
    }
}

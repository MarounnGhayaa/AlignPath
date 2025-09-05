<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPath extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'path_id',
        'progress_percentage',
        'date_saved'
    ];

    /**
     * Get the user that owns user-path.
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Expertise extends Model {
    protected $fillable = ['name'];
    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'user_expertises');
    }
}

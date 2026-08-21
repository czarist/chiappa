<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $table = 'media';
    protected $fillable = ['user_id', 'name', 'path', 'mime_type', 'size', 'alt_text'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

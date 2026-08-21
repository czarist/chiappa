<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    protected $fillable = ['page_id', 'type', 'position', 'active', 'content', 'settings'];
    protected function casts(): array { return ['active' => 'boolean', 'content' => 'array', 'settings' => 'array']; }
    public function page(): BelongsTo { return $this->belongsTo(Page::class); }
}

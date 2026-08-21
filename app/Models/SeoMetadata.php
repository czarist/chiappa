<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoMetadata extends Model
{
    protected $fillable = ['page_id', 'title', 'description', 'canonical_url', 'indexable', 'followable', 'og_title', 'og_description', 'og_image', 'twitter_card'];
    protected function casts(): array { return ['indexable' => 'boolean', 'followable' => 'boolean']; }
    public function page(): BelongsTo { return $this->belongsTo(Page::class); }
}

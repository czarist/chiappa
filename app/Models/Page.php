<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Page extends Model
{
    protected $fillable = ['title', 'slug', 'status', 'template', 'featured_image', 'published_at'];
    protected function casts(): array { return ['published_at' => 'datetime']; }
    public function sections(): HasMany { return $this->hasMany(PageSection::class)->orderBy('position'); }
    public function seo(): HasOne { return $this->hasOne(SeoMetadata::class); }
    public function isPublished(): bool { return $this->status === 'published' && ($this->published_at?->isPast() ?? true); }
}

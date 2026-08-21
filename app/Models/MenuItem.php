<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'page_id', 'label', 'url', 'target', 'position', 'active'];
    protected function casts(): array { return ['active' => 'boolean']; }
    public function menu(): BelongsTo { return $this->belongsTo(Menu::class); }
    public function page(): BelongsTo { return $this->belongsTo(Page::class); }
    public function destination(): string { return $this->page ? route('pages.show', $this->page->slug) : ($this->url ?: '#'); }
}

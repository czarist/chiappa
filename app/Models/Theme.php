<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = ['name', 'slug', 'settings', 'active'];
    protected function casts(): array { return ['settings' => 'array', 'active' => 'boolean']; }
}

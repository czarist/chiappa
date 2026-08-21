<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isAdministrator(): bool
    {
        return $this->role === 'administrator';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function roleLabel(): string
    {
        return $this->role === 'administrator' ? 'Administrador' : 'Editor';
    }

    public function statusLabel(): string
    {
        return $this->isActive() ? 'Ativo' : 'Inativo';
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $first = mb_substr($parts[0] ?? 'U', 0, 1);
        if (count($parts) < 2) {
            return mb_strtoupper(mb_substr($this->name ?: 'U', 0, 2));
        }

        return mb_strtoupper($first.mb_substr($parts[count($parts) - 1], 0, 1));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

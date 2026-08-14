<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_premium',
        'storage_limit',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_premium' => 'boolean',
            'storage_limit' => 'integer',
        ];
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get total storage used in bytes.
     */
    public function getStorageUsed(): int
    {
        return (int) $this->files()->sum('size');
    }

    /**
     * Max single file upload limit in bytes.
     * Free: 10 MB (10,485,760 bytes)
     * Premium: 100 MB (104,857,600 bytes)
     */
    public function getMaxFileUploadLimit(): int
    {
        return $this->is_premium ? 104857600 : 10485760;
    }

    /**
     * Get percentage of used storage space.
     */
    public function getStoragePercentage(): float
    {
        if ($this->storage_limit <= 0) return 0;
        $used = $this->getStorageUsed();
        return min(100, round(($used / $this->storage_limit) * 100, 1));
    }
}

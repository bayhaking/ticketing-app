<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'role', 'parent_promotor_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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

    /**
     * Relasi: staff belongs to promotor (parent).
     * Pakai $staff->promotor untuk akses akun promotor pemilik staff.
     */
    public function promotor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_promotor_id');
    }

    /**
     * Relasi: promotor has many staff.
     * Pakai $promotor->staffs untuk akses semua staff milik promotor.
     */
    public function staffs(): HasMany
    {
        return $this->hasMany(User::class, 'parent_promotor_id');
    }

    /**
     * Relasi: scans yang dilakukan oleh user ini (untuk staff scanner).
     */
    public function scans(): HasMany
    {
        return $this->hasMany(\App\Models\Order::class, 'scanned_by');
    }

    /**
     * Helper: cek apakah user ini staff.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Helper: cek apakah user ini promotor.
     */
    public function isPromotor(): bool
    {
        return $this->role === 'promotor';
    }

    /**
     * Helper: cek apakah user ini owner / super_owner.
     */
    public function isOwner(): bool
    {
        return in_array($this->role, ['owner', 'super_owner']);
    }
}
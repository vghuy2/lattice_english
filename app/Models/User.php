<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\IeltsType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'avatar',
        'current_band',
        'target_band',
        'test_type',
        'target_date',
        'study_days_per_week',
        'study_goal',
        'onboarding_completed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'role' => UserRole::class,
            'status' => UserStatus::class,
            'test_type' => IeltsType::class,
            'current_band' => 'float',
            'target_band' => 'float',
            'target_date' => 'date',
            'study_days_per_week' => 'integer',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::STUDENT;
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        // Return deterministic UI avatar initials
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=ffffff&bold=true";
    }

    public function scopeStudents(Builder $query): Builder
    {
        return $query->where('role', UserRole::STUDENT->value);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', UserRole::ADMIN->value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::ACTIVE->value);
    }
}

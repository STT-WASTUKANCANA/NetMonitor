<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
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
        ];
    }
    
    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }
    
    /**
     * Check if user is a petugas
     */
    public function isPetugas(): bool
    {
        return $this->hasRole('Petugas');
    }
    
    /**
     * Get user role name
     */
    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()->name ?? 'User';
    }
    
    /**
     * Get the profile photo URL
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        // Ensure we have a valid profile photo path and it's not just whitespace
        if ($this->profile_photo_path && trim($this->profile_photo_path) !== '') {
            return asset('storage/' . trim($this->profile_photo_path));
        }
        
        // Return default avatar if no profile photo is set
        return asset('images/default-profile.png');
    }
}

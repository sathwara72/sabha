<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
        'member_title_id',
        'phone',
        'city',
        'native_city',
        'birth_date',
        'anniversary_date',
        'residence_address',
        'avatar',
        'email_verified_at',
        'is_blocked',
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
            'is_blocked' => 'boolean',
            'birth_date' => 'date',
            'anniversary_date' => 'date',
        ];
    }

    public function business()
    {
        return $this->hasOne(Business::class, 'user_id');
    }

    public function memberTitle()
    {
        return $this->belongsTo(MemberTitle::class);
    }

    public function subAdminPermissions()
    {
        return $this->hasMany(SubAdminPermission::class);
    }

    /**
     * Full admins pass every check; sub-admins pass only for modules/abilities
     * explicitly granted in sub_admin_permissions; everyone else fails.
     */
    public function hasModuleAbility(string $module, string $ability): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        if ($this->role !== 'sub_admin') {
            return false;
        }

        return $this->subAdminPermissions()
            ->where('module', $module)
            ->where($ability, true)
            ->exists();
    }

    public function canAccessAdminArea(): bool
    {
        return in_array($this->role, ['admin', 'sub_admin'], true);
    }
}

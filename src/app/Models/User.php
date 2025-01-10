<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'user_id');
    }

    public function breakRecords()
    {
        return $this->hasManyThrough(BreakRecord::class, AttendanceRecord::class, 'user_id', 'attendance_record_id');
    }

    const ROLE_ADMIN = true;
    const ROLE_USER = false;

    public const STAFF_ROLES = [
        self::ROLE_ADMIN => 'admin',
        self::ROLE_USER => 'user',
    ];

    public function getRoleAttribute()
    {
        return self::STAFF_ROLES[$this->attributes['role']] ?? 'unknown';
    }
}

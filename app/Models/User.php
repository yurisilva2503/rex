<?php
// app/Models/User.php

namespace App\Models;

use App\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasPermissions;

    protected $fillable = [
        'name',
        'token',
        'email',
        'password',
        'email_verified_at',
        'last_login',
        'active',
        'blocked_until',
        'role',
        'department_id',
        'remember_token',
        'created_by',
        'updated_by',
        'is_admin'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'active' => 'boolean',
        'is_admin' => 'boolean',
        'blocked_until' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions', 'user_id', 'permission_id')
                    ->withTimestamps();
    }
}

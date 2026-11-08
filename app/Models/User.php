<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, \Illuminate\Auth\Passwords\CanResetPassword;

    protected $table = 'Users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone_number',
        'ID',
        'role_id',
        'section_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role_id' => 'integer',
        'section_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public $timestamps = true;
    
    // Override Laravel's default timestamp columns since our table only has created_at
    const UPDATED_AT = null;

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    public function reportedIssues()
    {
        return $this->hasMany(Issue::class, 'reported_by_user_id', 'user_id');
    }

    public function assignedIssues()
    {
        return $this->hasMany(Issue::class, 'assigned_to_user_id', 'user_id');
    }

    public function issueUpdates()
    {
        return $this->hasMany(IssueUpdate::class, 'user_id', 'user_id');
    }

    public function issueUpvotes()
    {
        return $this->hasMany(IssueUpvote::class, 'user_id', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notify::class, 'receiver_id', 'user_id');
    }

    // Accessor for first name
    public function getFirstNameAttribute()
    {
        $names = explode(' ', $this->full_name, 2);
        return $names[0] ?? '';
    }

    // Accessor for last name
    public function getLastNameAttribute()
    {
        $names = explode(' ', $this->full_name, 2);
        return $names[1] ?? '';
    }
}

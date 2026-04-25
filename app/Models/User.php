<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'cni_number',
        'birth_date',
        'birth_place',
        'address',
        'profession',
        'nationality',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
    ];

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function validatedRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'validated_by');
    }

    public function rejectedRequests()
    {
        return $this->hasMany(DocumentRequest::class, 'rejected_by');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCitizen()
    {
        return $this->role === 'citizen';
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function scopeCitizens($query)
    {
        return $query->where('role', 'citizen');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'document_type',
        'first_name',
        'last_name',
        'birth_date',
        'birth_place',
        'address',
        'phone',
        'status',
        'priority',
        'notes',
        'rejection_reason',
        'validated_at',
        'rejected_at',
        'validated_by',
        'rejected_by',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'validated_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function document()
    {
        return $this->hasOne(Document::class, 'request_id');
    }

    public function getDocumentTypeLabelAttribute()
    {
        return match($this->document_type) {
            'cni' => 'Carte Nationale d\'Identité',
            'passeport' => 'Passeport',
            'permis' => 'Permis de conduire',
            default => $this->document_type,
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'en cours' => 'En cours',
            'validée' => 'Validée',
            'rejetée' => 'Rejetée',
            default => $this->status,
        };
    }

    public function getPriorityLabelAttribute()
    {
        return match($this->priority) {
            'urgent' => 'Urgent',
            'normal' => 'Normal',
            default => $this->priority,
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'en cours');
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validée');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejetée');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeNormal($query)
    {
        return $query->where('priority', 'normal');
    }

    public function canBeValidated()
    {
        return $this->status === 'en cours';
    }

    public function canBeRejected()
    {
        return $this->status === 'en cours';
    }

    public function isProcessed()
    {
        return in_array($this->status, ['validée', 'rejetée']);
    }

    public function generateReference()
    {
        $prefix = strtoupper($this->document_type);
        $year = date('Y');
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$random}";
    }
}

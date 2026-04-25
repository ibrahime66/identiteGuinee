<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'request_id',
        'document_type',
        'holder_name',
        'birth_date',
        'birth_place',
        'issue_date',
        'expiry_date',
        'qr_code',
        'is_valid',
        'revoked_at',
        'revocation_reason',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'revoked_at' => 'datetime',
        'is_valid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function request()
    {
        return $this->belongsTo(DocumentRequest::class);
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
        if ($this->revoked_at) {
            return 'Révoqué';
        }
        
        return $this->is_valid ? 'Valide' : 'Invalide';
    }

    public function isValid()
    {
        return $this->is_valid && !$this->revoked_at && $this->expiry_date > now();
    }

    public function isExpired()
    {
        return $this->expiry_date < now();
    }

    public function isRevoked()
    {
        return $this->revoked_at !== null;
    }

    public function revoke($reason = null)
    {
        $this->update([
            'is_valid' => false,
            'revoked_at' => now(),
            'revocation_reason' => $reason,
        ]);
    }

    public function generateQrCode()
    {
        return 'DOC-' . strtoupper($this->document_type) . '-' . str_pad($this->id, 8, '0', STR_PAD_LEFT);
    }

    public function generateReference()
    {
        $prefix = strtoupper($this->document_type);
        $year = $this->issue_date->format('Y');
        $random = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$random}";
    }

    public function scopeValid($query)
    {
        return $query->where('is_valid', true)
                    ->whereNull('revoked_at')
                    ->where('expiry_date', '>', now());
    }

    public function scopeInvalid($query)
    {
        return $query->where(function($q) {
            $q->where('is_valid', false)
              ->orWhereNotNull('revoked_at')
              ->orWhere('expiry_date', '<', now());
        });
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}

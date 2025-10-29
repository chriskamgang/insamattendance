<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacataireContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vacataire_contracts';

    protected $fillable = [
        'user_id',
        'contract_number',
        'start_date',
        'end_date',
        'hourly_rate',
        'max_hours_per_month',
        'specialization',
        'contract_type',
        'status',
        'termination_reason',
        'terminated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'terminated_at' => 'datetime',
        'hourly_rate' => 'decimal:2',
    ];

    // Relation avec l'utilisateur vacataire
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation avec le créateur
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation avec le modificateur
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Vérifier si le contrat est actif
    public function isActive()
    {
        return $this->status === 'active' &&
               (!$this->end_date || $this->end_date >= now()->toDateString());
    }

    // Vérifier si le contrat est expiré
    public function isExpired()
    {
        return $this->end_date && $this->end_date < now()->toDateString();
    }
}

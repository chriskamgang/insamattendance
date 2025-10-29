<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacataireMonthlyPayment extends Model
{
    use HasFactory;

    protected $table = 'vacataire_monthly_payments';

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'total_hours',
        'total_days_worked',
        'hourly_rate',
        'gross_salary',
        'deductions',
        'bonuses',
        'net_salary',
        'status',
        'validated_by',
        'validated_at',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'total_hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'deductions' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'validated_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relation avec l'utilisateur vacataire
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation avec le validateur
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Vérifier si le paiement est en attente
    public function isPending()
    {
        return $this->status === 'pending';
    }

    // Vérifier si le paiement est validé
    public function isValidated()
    {
        return $this->status === 'validated';
    }

    // Vérifier si le paiement est effectué
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    // Obtenir le nom du mois en français
    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        return $months[$this->month] ?? '';
    }

    // Obtenir la période complète
    public function getPeriodAttribute()
    {
        return $this->month_name . ' ' . $this->year;
    }
}

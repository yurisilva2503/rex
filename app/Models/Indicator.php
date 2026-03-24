<?php
// app/Models/Indicator.php

namespace App\Models;

use App\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Indicator extends Model
{
    use HasFactory, SoftDeletes, HasPermissions;

    protected $table = 'indicators';

    protected $fillable = [
        'department_id',
        'name',
        'type',
        'goal',
        'unit',
        'description',
        'formula',
        'direction',
        'active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'goal' => 'decimal:4',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constants
    const TYPE_STRATEGIC = 'strategic';
    const TYPE_TACTICAL = 'tactical';
    const TYPE_MONITORING = 'monitoring';

    const DIRECTION_HIGHER_BETTER = 'higher_is_better';
    const DIRECTION_LOWER_BETTER = 'lower_is_better';

    const UNIT_PERCENT = '%';
    const UNIT_UNIT = 'un';
    const UNIT_REAL = 'R$';
    const UNIT_TON = 'T';
    const UNIT_KG = 'Kg';
    const UNIT_CT = 'Ct';

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function values()
    {
        return $this->hasMany(IndicatorValue::class);
    }

    public function analyses()
    {
        return $this->hasMany(related: Analysis::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeStrategic($query)
    {
        return $query->where('type', self::TYPE_STRATEGIC);
    }

    public function scopeTactical($query)
    {
        return $query->where('type', self::TYPE_TACTICAL);
    }

    public function scopeMonitoring($query)
    {
        return $query->where('type', self::TYPE_MONITORING);
    }

    public function scopeWithValuesForYear($query, int $year)
    {
        return $query->with(['values' => function ($q) use ($year) {
            $q->where('year', $year)->orderBy('month');
        }]);
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_STRATEGIC => 'Estratégico',
            self::TYPE_TACTICAL => 'Tático',
            self::TYPE_MONITORING => 'Monitoramento',
            default => $this->type
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_STRATEGIC => '#499AFD',
            self::TYPE_TACTICAL => '#FE4949',
            self::TYPE_MONITORING => '#d89c00',
            default => '#f3f4f6'
        };
    }

    public function getTypeTextColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_STRATEGIC => '#ffffff',
            self::TYPE_TACTICAL => '#ffffff',
            self::TYPE_MONITORING => '#ffffff',
            default => '#374151'
        };
    }

    public function getDirectionLabelAttribute(): string
    {
        return match($this->direction) {
            self::DIRECTION_HIGHER_BETTER => 'Maior é melhor',
            self::DIRECTION_LOWER_BETTER => 'Menor é melhor',
            default => $this->direction
        };
    }

    // Methods
    public function getValueForMonth(int $year, int $month): ?IndicatorValue
    {
        return $this->values()
            ->where('year', $year)
            ->where('month', $month)
            ->first();
    }

    public function getValuesForYear(int $year)
    {
        return $this->values()
            ->where('year', $year)
            ->orderBy('month')
            ->get();
    }

    public function calculateStatus($value): string
    {
        if ($value === null || $value === '') {
            return 'no_data';
        }

        $numValue = (float) $value;

        if ($this->direction === self::DIRECTION_LOWER_BETTER) {
            // Para indicadores onde menor é melhor (custo, reclamações, etc)
            if ($numValue <= $this->goal) {
                return 'on_target';
            }
            if ($numValue <= $this->goal * 1.5) {
                return 'near_target';
            }
            return 'below_target';
        }

        // Para indicadores onde maior é melhor (default)
        if ($numValue >= $this->goal) {
            return 'on_target';
        }
        if ($numValue >= $this->goal * 0.8) {
            return 'near_target';
        }
        return 'below_target';
    }

    public function getStatusColorAttribute(): array
    {
        return [
            'on_target' => '#10b981',
            'near_target' => '#d89c00',
            'below_target' => '#ef4444',
            'no_data' => '#e5e7eb'
        ];
    }

    public function getMonthlyAverages(int $year): array
    {
        $values = $this->getValuesForYear($year);
        $averages = [];

        for ($month = 1; $month <= 12; $month++) {
            $monthValue = $values->firstWhere('month', $month);
            $averages[$month] = $monthValue ? (float) $monthValue->value : null;
        }

        return $averages;
    }
}

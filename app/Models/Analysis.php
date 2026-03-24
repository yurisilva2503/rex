<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Analysis extends Model
{
    use HasFactory;

    protected $table = 'analyses';

    protected $fillable = [
        'indicator_id',
        'year',
        'month',
        'analysis',
        'insights',
        'trend',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Constants for trends
    const TREND_UP = 'up';
    const TREND_DOWN = 'down';
    const TREND_STABLE = 'stable';
    const TREND_VOLATILE = 'volatile';

    // Relationships
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function actionPlans(): HasMany
    {
        return $this->hasMany(ActionPlan::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeForMonth($query, int $month)
    {
        return $query->where('month', $month);
    }

    public function scopeWithActionPlans($query)
    {
        return $query->with('actionPlans');
    }

    public function scopeWithPendingActions($query)
    {
        return $query->whereHas('actionPlans', function ($q) {
            $q->whereIn('status', ['pending', 'in_progress', 'delayed']);
        });
    }

    // Accessors
    public function getMonthNameAttribute(): string
    {
        return match($this->month) {
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
            default => 'Mês Inválido'
        };
    }

    public function getTrendLabelAttribute(): ?string
    {
        return match($this->trend) {
            self::TREND_UP => '📈 Crescente',
            self::TREND_DOWN => '📉 Decrescente',
            self::TREND_STABLE => '➡️ Estável',
            self::TREND_VOLATILE => '📊 Volátil',
            default => null
        };
    }

    public function getTrendColorAttribute(): ?string
    {
        return match($this->trend) {
            self::TREND_UP => '#10b981',
            self::TREND_DOWN => '#ef4444',
            self::TREND_STABLE => '#6b7280',
            self::TREND_VOLATILE => '#f59e0b',
            default => null
        };
    }

    // Methods
    public function getActionPlansCountByStatus(): array
    {
        return [
            'pending' => $this->actionPlans()->where('status', 'pending')->count(),
            'in_progress' => $this->actionPlans()->where('status', 'in_progress')->count(),
            'completed' => $this->actionPlans()->where('status', 'completed')->count(),
            'delayed' => $this->actionPlans()->where('status', 'delayed')->count(),
        ];
    }

    public function hasActiveActions(): bool
    {
        return $this->actionPlans()
            ->whereIn('status', ['pending', 'in_progress', 'delayed'])
            ->exists();
    }

    public function getDelayedActionsCount(): int
    {
        return $this->actionPlans()
            ->where('status', 'delayed')
            ->count();
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorValue extends Model
{
    use HasFactory;

    protected $table = 'indicator_values';

    protected $fillable = [
        'indicator_id',
        'year',
        'month',
        'value',
        'status',
        'notes',
        'updated_by'
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'year' => 'integer',
        'month' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
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

    public function scopeForPeriod($query, int $year, ?int $month = null)
    {
        $query->where('year', $year);

        if ($month) {
            $query->where('month', $month);
        }

        return $query;
    }

    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
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

    public function getMonthShortNameAttribute(): string
    {
        return match($this->month) {
            1 => 'Jan', 2 => 'Fev', 3 => 'Mar',
            4 => 'Abr', 5 => 'Mai', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Set',
            10 => 'Out', 11 => 'Nov', 12 => 'Dez',
            default => '???'
        };
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->value === null) {
            return '-';
        }

        return number_format($this->value, 2, ',', '.') . $this->indicator->unit;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'on_target' => 'Meta Atingida',
            'near_target' => 'Próximo à Meta',
            'below_target' => 'Abaixo da Meta',
            'no_data' => 'Sem Dados',
            default => $this->status ?? 'Sem Dados'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'on_target' => '#10b981',
            'near_target' => '#d89c00',
            'below_target' => '#ef4444',
            default => '#e5e7eb'
        };
    }

    // Methods
    public function updateStatus(): void
    {
        if ($this->indicator) {
            $this->status = $this->indicator->calculateStatus($this->value);
            $this->saveQuietly(); // Save without firing events
        }
    }

    protected static function booted()
    {
        static::saving(function ($indicatorValue) {
            // Auto-calculate status when value is being saved
            if ($indicatorValue->isDirty('value') && $indicatorValue->indicator) {
                $indicatorValue->status = $indicatorValue->indicator->calculateStatus($indicatorValue->value);
            }
        });
    }
}

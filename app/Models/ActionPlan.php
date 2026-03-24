<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'action_plans';

    protected $fillable = [
        'analysis_id',
        'action',
        'responsible',
        'deadline',
        'status',
        'comments',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELAYED = 'delayed';

    // Relationships
    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class); // Laravel assume foreign key 'analysis_id' automaticamente
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
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeDelayed($query)
    {
        return $query->where('status', self::STATUS_DELAYED);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_DELAYED]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
            ->whereNotIn('status', [self::STATUS_COMPLETED]);
    }

    public function scopeByResponsible($query, string $responsible)
    {
        return $query->where('responsible', 'like', "%{$responsible}%");
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_IN_PROGRESS => 'Em Andamento',
            self::STATUS_COMPLETED => 'Concluído',
            self::STATUS_DELAYED => 'Atrasado',
            default => $this->status
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => '#6b7280',
            self::STATUS_IN_PROGRESS => '#3b82f6',
            self::STATUS_COMPLETED => '#10b981',
            self::STATUS_DELAYED => '#ef4444',
            default => '#6b7280'
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => '⏳',
            self::STATUS_IN_PROGRESS => '🔄',
            self::STATUS_COMPLETED => '✅',
            self::STATUS_DELAYED => '⚠️',
            default => '❓'
        };
    }

    public function getFormattedDeadlineAttribute(): string
    {
        return $this->deadline ? $this->deadline->format('d/m/Y') : 'Sem prazo';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline &&
            $this->deadline->isPast() &&
            $this->status !== self::STATUS_COMPLETED;
    }

    public function getDaysUntilDeadlineAttribute(): ?int
    {
        if (!$this->deadline) {
            return null;
        }

        return now()->startOfDay()->diffInDays($this->deadline, false);
    }

    // Methods
    public function markAsCompleted(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function markAsInProgress(): void
    {
        $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    public function markAsDelayed(): void
    {
        $this->update(['status' => self::STATUS_DELAYED]);
    }

    public function checkAndUpdateDelayStatus(): void
    {
        if (
            $this->deadline &&
            $this->deadline->isPast() &&
            !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_DELAYED])
        ) {
            $this->markAsDelayed();
        }
    }

    protected static function booted()
    {
        static::saving(function ($actionPlan) {
            // Auto-check for delay
            if (
                $actionPlan->deadline &&
                $actionPlan->deadline->isPast() &&
                !in_array($actionPlan->status, [self::STATUS_COMPLETED, self::STATUS_DELAYED])
            ) {
                $actionPlan->status = self::STATUS_DELAYED;
            }
        });
    }
}

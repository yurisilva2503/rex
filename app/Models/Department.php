<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'description',
        'icon',
        'active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];


    // Relationships
    public function indicators()
    {
        return $this->hasMany(Indicator::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeWithIndicatorsCount($query)
    {
        return $query->withCount('indicators');
    }

    // Accessors
    public function getIndicatorsCountAttribute(): int
    {
        return $this->indicators()->count();
    }

    public function getActiveIndicatorsCountAttribute(): int
    {
        return $this->indicators()->where('active', true)->count();
    }

    // Methods
    public function hasIndicators(): bool
    {
        return $this->indicators()->exists();
    }

    public function deactivate(): void
    {
        $this->update(['active' => false]);
    }

    public function activate(): void
    {
        $this->update(['active' => true]);
    }
}

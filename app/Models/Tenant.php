<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'slug', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $slugBase = Str::slug($tenant->name);
                $slug = $slugBase;
                $counter = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $slugBase . '-' . $counter++;
                }

                $tenant->slug = $slug;
            }
        });

        static::updating(function ($tenant) {
            if (!$tenant->isDirty('slug')) {
                // Mantém o slug original para evitar mudança involuntária
                $tenant->slug = $tenant->getOriginal('slug');
            } else {
                // slug alterado manualmente, recalcula para garantir unicidade
                $slugBase = Str::slug($tenant->slug);
                $slug = $slugBase;
                $counter = 1;

                while (static::where('slug', $slug)->where('id', '!=', $tenant->id)->exists()) {
                    $slug = $slugBase . '-' . $counter++;
                }

                $tenant->slug = $slug;
            }
        });

    }

    public function setIsActiveAttribute($value)
    {
        $this->attributes['is_active'] = $value ? 1 : 0;
    }


    public function getRouteKeyName(): string
    {
        return 'id';
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }
}
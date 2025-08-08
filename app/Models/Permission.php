<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Support\Str;

class Permission extends SpatiePermission
{
    public $incrementing = false;      // Chave primária não é incrementada
    protected $keyType = 'string';    // Chave primária é string (UUID)

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Só gera UUID se não estiver definido
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
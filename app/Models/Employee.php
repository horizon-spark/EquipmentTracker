<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $table = 'employee';

    protected $primaryKey = 'id_employee';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'id_location',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(
            Location::class,
            'id_location',
            'id_location'
        );
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(
            Equipment::class,
            'id_employee',
            'id_employee'
        );
    }
}
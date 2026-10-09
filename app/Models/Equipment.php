<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $primaryKey = 'id_equipment';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'id_type',
        'inv_number',
        'assignment_date',
        'id_employee',
    ];

    protected $casts = [
        'assignment_date' => 'date',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(
            TypeEquipment::class,
            'id_type',
            'id_type'
        );
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            Employee::class,
            'id_employee',
            'id_employee'
        );
    }

    public function services(): HasMany
    {
        return $this->hasMany(
            Service::class,
            'id_equipment',
            'id_equipment'
        );
    }
}
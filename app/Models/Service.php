<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $table = 'service';

    protected $primaryKey = 'id_service';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'data_service',
        'description',
        'id_equipment',
    ];

    protected $casts = [
        'data_service' => 'date',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(
            Equipment::class,
            'id_equipment',
            'id_equipment'
        );
    }

    public function supplies(): BelongsToMany
    {
        return $this->belongsToMany(
            Supply::class,
            'service_supplies',   // pivot-таблица
            'id_service',         // FK на текущую модель в pivot
            'id_supply'           // FK на связанную модель в pivot
        )
            ->withPivot('quantity_used');
    }
}
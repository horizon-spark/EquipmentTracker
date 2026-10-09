<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeEquipment extends Model
{
    protected $table = 'type_equipment';

    protected $primaryKey = 'id_type';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'name_type',
        'manufacturer',
        'model',
        'serial_number',
    ];

    public function equipment(): HasMany
    {
        return $this->hasMany(
            Equipment::class,
            'id_type',
            'id_type'
        );
    }
}
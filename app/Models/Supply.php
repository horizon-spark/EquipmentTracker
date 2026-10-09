<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supply extends Model
{
    protected $table = 'supplies';

    protected $primaryKey = 'id_supply';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'name_supplies',
        'quantity',
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'service_supplies',
            'id_supply',
            'id_service'
        )
            ->withPivot('quantity_used');
    }
}
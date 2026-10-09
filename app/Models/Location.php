<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $table = 'location';

    protected $primaryKey = 'id_location';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'city',
        'office',
        'address',
    ];

    public $timestamps = false;

    public function employees(): HasMany
    {
        return $this->hasMany(
            Employee::class,
            'id_location',
            'id_location'
        );
    }
}
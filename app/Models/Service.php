<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Service extends Model
{
    use HasFactory;

    protected $table = 'service';

    protected $primaryKey = 'id_service';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'service_date',
        'description',
        'id_equipment',
    ];

    protected $casts = [
        'service_date' => 'date',
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
            ->using(ServiceSupply::class)
            ->withPivot('quantity_used');
    }

    /**
     * Перед удалением обслуживания вернуть все израсходованные расходники на склад.
     */
    public function delete()
    {
        return DB::transaction(function () {
            $this->supplies()->get()->each(function (Supply $supply): void {
                $supply->pivot->delete();
            });

            $deleted = parent::delete();

            if (! $deleted) {
                throw new \RuntimeException('Не удалось удалить запись обслуживания.');
            }

            return $deleted;
        });
    }
}

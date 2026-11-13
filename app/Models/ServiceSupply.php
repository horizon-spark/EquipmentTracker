<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ServiceSupply extends Pivot
{
    protected $table = 'service_supplies';

    protected $fillable = [
        'id_service',
        'id_supply',
        'quantity_used',
    ];

    protected $casts = [
        'quantity_used' => 'integer',
    ];

    /**
     * Списать разницу между старым и новым количеством при сохранении.
     */
    public function save(array $options = []): bool
    {
        return DB::transaction(function () use ($options): bool {
            $newQuantity = (int) ($this->quantity_used ?? 1);

            if ($newQuantity < 1) {
                throw ValidationException::withMessages([
                    'quantity_used' => 'Количество израсходованного расходника должно быть не меньше 1.',
                ]);
            }

            $this->quantity_used = $newQuantity;

            $originalQuantity = $this->exists ? (int) $this->getOriginal('quantity_used') : 0;
            $delta = $newQuantity - $originalQuantity;

            if ($delta > 0) {
                $this->consumeStock($delta);
            } elseif ($delta < 0) {
                $this->restoreStock(-$delta);
            }

            return parent::save($options);
        });
    }

    /**
     * Вернуть израсходованное количество на склад при отвязке расходника.
     */
    public function delete()
    {
        return DB::transaction(function () {
            $result = parent::delete();

            if ($result) {
                $this->restoreStock((int) $this->quantity_used);
            }

            return $result;
        });
    }

    private function consumeStock(int $quantity): void
    {
        $supply = Supply::query()
            ->whereKey($this->id_supply)
            ->lockForUpdate()
            ->firstOrFail();

        if ($supply->quantity < $quantity) {
            throw ValidationException::withMessages([
                'quantity_used' => "Недостаточно расходника «{$supply->name_supplies}»: остаток {$supply->quantity}, требуется {$quantity}.",
            ]);
        }

        $supply->decrement('quantity', $quantity);
    }

    private function restoreStock(int $quantity): void
    {
        if ($quantity < 1) {
            return;
        }

        Supply::query()
            ->whereKey($this->id_supply)
            ->increment('quantity', $quantity);
    }
}

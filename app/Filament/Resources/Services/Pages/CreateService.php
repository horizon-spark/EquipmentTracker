<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $supplyItems = $data['supplies'] ?? [];
        unset($data['supplies']);

        $service = static::getModel()::create($data);

        try {
            foreach ($supplyItems as $item) {
                $service->supplies()->attach($item['id_supply'], [
                    'quantity_used' => (int) $item['quantity_used'],
                ]);
            }
        } catch (ValidationException $exception) {
            $message = collect($exception->errors())->flatten()->first();

            Notification::make()
                ->danger()
                ->title('Не удалось списать расходники')
                ->body($message)
                ->send();

            throw ValidationException::withMessages([
                'data.supplies' => $message,
            ]);
        }

        return $service;
    }
}

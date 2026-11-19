<?php

namespace App\Filament\Imports;

use DateTimeImmutable;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Number;

abstract class BaseImporter extends Importer
{
    protected static string $entityLabel = 'записей';

    protected static bool $shouldPreventFormulaInjection = true;

    /**
     * @return array<int, Checkbox>
     */
    public static function getOptionsFormComponents(): array
    {
        return [
            Checkbox::make('updateExisting')
                ->label('Обновлять существующие записи')
                ->helperText('Если выключено, будут создаваться только новые записи.')
                ->default(true),
        ];
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Импорт '.static::$entityLabel.' завершён';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Успешно импортировано строк: '.Number::format($import->successful_rows).'.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' Ошибок: '.Number::format($failedRowsCount).'.';
        }

        return $body;
    }

    /**
     * Приводит дату из CSV к формату базы данных.
     *
     * Поддерживаются форматы дд.мм.гггг, дд/мм/гггг, гггг-мм-дд и гггг.мм.дд.
     */
    protected static function parseDate(mixed $state): ?string
    {
        if (blank($state)) {
            return null;
        }

        $value = trim((string) $state);

        foreach (['d.m.Y', 'd/m/Y', 'Y-m-d', 'Y.m.d'] as $format) {
            $date = DateTimeImmutable::createFromFormat('!'.$format, $value);

            if (($date !== false) && ($date->format($format) === $value)) {
                return $date->format('Y-m-d');
            }
        }

        return $value;
    }

    protected function shouldUpdateExisting(): bool
    {
        return (bool) ($this->options['updateExisting'] ?? true);
    }
}

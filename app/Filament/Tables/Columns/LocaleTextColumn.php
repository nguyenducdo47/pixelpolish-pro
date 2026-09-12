<?php

namespace App\Filament\Tables\Columns;

use App\Support\UiLocale;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

final class LocaleTextColumn
{
    public static function make(string $name): TextColumn
    {
        return TextColumn::make($name)
            ->state(function (Model $record) use ($name): string {
                $segments = explode('.', $name);
                $attribute = array_pop($segments);
                $model = $segments === []
                    ? $record
                    : data_get($record, implode('.', $segments));

                if (! is_object($model) || ! method_exists($model, 'localeText')) {
                    return '';
                }

                return $model->localeText($attribute, UiLocale::current());
            });
    }
}

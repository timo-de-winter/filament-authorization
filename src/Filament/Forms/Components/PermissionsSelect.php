<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Forms\Components;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;

class PermissionsSelect
{
    public static function make($name): Tabs|Section
    {
        // If there are multiple tabs we should show tabs, otherwise instantly the grid
        $tabs = FilamentAuthorization::getTabs();

        if (count($tabs) === 0) {
            return Section::make();
        }

        return count($tabs) > 1
            ? self::getTabs($name, $tabs)
            : self::getPrefixGrid($name, $tabs[0]);
    }

    private static function getTabs(string $name, array $tabs): Tabs
    {
        return Tabs::make()
            ->tabs(array_map(function (string $tab) use ($name) {
                return Tabs\Tab::make($tab)
                    ->schema([
                        self::getPrefixGrid($name, $tab, true),
                    ]);
            }, $tabs));
    }

    private static function getPrefixGrid(string $name, string $tab, bool $asGrid = false): Grid|Section
    {
        $class = $asGrid
            ? Grid::class
            : Section::class;

        return $class::make()
            ->columns([
                'xs' => 1,
                'md' => 2,
                'lg' => 4,
            ])
            ->schema(
                collect(FilamentAuthorization::getPrefixGroups($tab))
                    ->map(function (string $group) use ($name, $tab) {
                        return Section::make(FilamentAuthorization::getPrefixTranslation($group))
                            ->collapsible()
                            ->collapsed()
                            ->columnSpan(1)
                            ->headerActions([
                                Action::make('selected')
                                    ->badge()
                                    ->label(function (Get $get) use ($tab, $group, $name) {
                                        return collect($allPermissions = FilamentAuthorization::getPermissions($tab, $group))->filter(fn (string|int $permission, int|string $key) => $get(implode('.', [$name, $group, $key])))->count() . ' / ' . count($allPermissions);
                                    })
                                    ->disabled()
                                    ->color('info'),
                                Action::make('toggleAll')
                                    ->iconButton()
                                    ->icon(function (Get $get) use ($tab, $group, $name) {
                                        return collect(FilamentAuthorization::getPermissions($tab, $group))->filter(fn (string|int $permission, int|string $key) => $get(implode('.', [$name, $group, $key])))->count()
                                            ? 'heroicon-o-bars-arrow-down'
                                            : 'heroicon-o-bars-arrow-up';
                                    })
                                    ->action(function (Set $set, Get $get) use ($name, $tab, $group) {
                                        $allEnabled = collect(FilamentAuthorization::getPermissions($tab, $group))->filter(fn (string|int $permission, int|string $key) => $get(implode('.', [$name, $group, $key])))->count();

                                        foreach (FilamentAuthorization::getPermissions($tab, $group) as $key => $permission) {
                                            $set(implode('.', [$name, $group, $key]), !$allEnabled);
                                        }
                                    })
                            ])
                            ->schema(function () use ($name, $tab, $group) {
                                return collect(FilamentAuthorization::getPermissions($tab, $group))
                                    ->map(function (string|int $permission, int|string $key) use ($name, $group) {
                                        return Checkbox::make(implode('.', [$name, $group, $key]))
                                            ->live()
                                            ->label(is_string($permission) ? $permission : ucfirst($key));
                                    })
                                    ->toArray();
                            });
                    })
                    ->toArray(),
            );
    }
}

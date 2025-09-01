<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Forms\Components;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
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
                'lg' => 3,
            ])
            ->schema(
                collect(FilamentAuthorization::getPrefixGroups($tab))
                    ->map(function (string $group) use ($name, $tab) {
                        return Fieldset::make(ucfirst($group))
                            ->columnSpan(1)
                            ->schema(function () use ($name, $tab, $group) {
                                return collect(FilamentAuthorization::getPermissions($tab, $group))
                                    ->map(function (string $permission, int|string $key) use ($name, $group) {
                                        return Checkbox::make(implode('.', [$name, $group, $permission]))
                                            ->label(is_string($key) ? $key : ucfirst($permission));
                                    })
                                    ->toArray();
                            });
                    })
                    ->toArray(),
            );
    }
}

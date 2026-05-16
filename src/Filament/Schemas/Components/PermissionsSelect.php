<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Schemas\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                return Tab::make($tab)
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
                'lg' => 2,
            ])
            ->components(
                collect(FilamentAuthorization::getPrefixGroups($tab))
                    ->map(function (string $group) use ($name, $tab) {
                        return Section::make(FilamentAuthorization::getPrefixTranslation($group))
                            ->description(FilamentAuthorization::getPrefixDescription($group))
                            ->collapsible()
                            ->columnSpan(1)
                            ->key('permission-group-'.$group)
                            ->headerActions([
                                Action::make('selected')
                                    ->badge()
                                    ->label(function (Get $get) use ($tab, $group, $name) {
                                        return collect($allPermissions = FilamentAuthorization::getPermissions($tab, $group))->filter(fn (string|int $permission, int|string $key) => $get(implode('.', [$name, $group, $key])))->count().' / '.count($allPermissions);
                                    })
                                    ->disabled()
                                    ->color('info'),
                                Action::make('toggleAll')
                                    ->iconButton()
                                    ->icon(function (Get $get) use ($tab, $group, $name) {
                                        $all = FilamentAuthorization::getPermissions($tab, $group);
                                        $selected = collect($all)->filter(fn (string|int $permission, int|string $key) => $get(implode('.', [$name, $group, $key])))->count();

                                        return match (true) {
                                            $selected === 0 => 'heroicon-o-stop',
                                            $selected === count($all) => 'heroicon-s-check-circle',
                                            default => 'heroicon-s-minus-circle',
                                        };
                                    })
                                    ->color(function (Get $get) use ($tab, $group, $name) {
                                        $all = FilamentAuthorization::getPermissions($tab, $group);
                                        $selected = collect($all)->filter(fn (string|int $permission, int|string $key) => $get(implode('.', [$name, $group, $key])))->count();

                                        return match (true) {
                                            $selected === 0 => 'gray',
                                            $selected === count($all) => 'success',
                                            default => 'warning',
                                        };
                                    })
                                    ->action(function (Set $set, Get $get) use ($name, $tab, $group) {
                                        $allEnabled = collect(FilamentAuthorization::getPermissions($tab, $group))->filter(fn (string|int $permission, int|string $key) => $get(implode('.', [$name, $group, $key])))->count();

                                        foreach (FilamentAuthorization::getPermissions($tab, $group) as $key => $permission) {
                                            $set(implode('.', [$name, $group, $key]), ! $allEnabled);
                                        }
                                    }),
                            ])
                            ->components(function () use ($name, $tab, $group) {
                                return collect(FilamentAuthorization::getPermissions($tab, $group))
                                    ->map(function (string|int $permission, int|string $key) use ($name, $group) {
                                        return Checkbox::make(implode('.', [$name, $group, $key]))
                                            ->live()
                                            ->label(is_string($permission) ? $permission : ucfirst((string) $key))
                                            ->helperText(FilamentAuthorization::getDescription($group, $key));
                                    })
                                    ->toArray();
                            });
                    })
                    ->toArray(),
            );
    }
}

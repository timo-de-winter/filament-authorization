<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles;

use Filament\Resources\ResourceConfiguration;

class RoleResourceConfiguration extends ResourceConfiguration
{
    protected ?string $cluster = null;

    protected ?string $navigationGroup = null;

    public function cluster(?string $cluster): static
    {
        $this->cluster = $cluster;

        return $this;
    }

    public function getCluster(): ?string
    {
        return $this->cluster;
    }

    public function navigationGroup(?string $navigationGroup): static
    {
        $this->navigationGroup = $navigationGroup;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }
}

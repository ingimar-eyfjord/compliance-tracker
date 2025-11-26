<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;

trait ScopedByOrganization
{
    /**
     * Limit the query to the provided organization (model or UUID string).
     */
    public function scopeForOrganization(Builder $query, Organization|string $organization): Builder
    {
        $organizationId = $organization instanceof Organization
            ? $organization->getKey()
            : $organization;

        return $query->where($this->getTable() . '.organization_id', $organizationId);
    }

    /**
     * Helper to assign the organization identifier in a fluent manner.
     */
    public function forOrganization(Organization|string $organization): static
    {
        $organizationId = $organization instanceof Organization
            ? $organization->getKey()
            : $organization;

        $this->setAttribute('organization_id', $organizationId);

        return $this;
    }
}

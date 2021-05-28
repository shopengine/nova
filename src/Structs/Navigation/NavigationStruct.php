<?php

namespace Brainspin\Novashopengine\Structs\Navigation;

// @todo: on php8 use constructor property promotion
use Illuminate\Support\Collection;

final class NavigationStruct {

    private Collection $groups;

    /**
     * NavigationStruct constructor.
     *
     * @param array $groups
     */
    public function __construct(
        array $groups
    ) {
        $this->groups = collect($groups);
    }

    /**
     * @return array
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getAvailableStruct(array $availableResources) : NavigationStruct
    {
        return new NavigationStruct(
            $this->groups->map(fn(NavigationGroupStruct $group) => $group->getAvailableGroup($availableResources))->filter()->all()
        );
    }

}

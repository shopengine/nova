<?php

namespace ShopEngine\Nova\Structs\Navigation;

// @todo: on php8 use constructor property promotion
use Illuminate\Support\Collection;

final class NavigationStruct implements \IteratorAggregate {

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

    public function hasGroupWithTitle($title): int
    {
        return $this->groups->search(
            fn($item, $key) => $item->getTitle() === $title
        );
    }

    public function getAvailableStruct(array $availableResources) : NavigationStruct
    {
        return new NavigationStruct(
            $this->groups->map(fn(NavigationGroupStruct $group) => $group->getAvailableGroup($availableResources))->filter()->all()
        );
    }

    public function mergeWith(NavigationStruct $targetStruct) : self
    {
        foreach ($targetStruct as $structGroup) {
            $key = $this->hasGroupWithTitle($structGroup->getTitle());
            if ($key) {
                $this->groups->get($key)->addItems($structGroup->getItems()->toArray());
            } else {
                $this->groups->add($structGroup);
            }
        }
        return $this;
    }


    public function getIterator()
    {
        return new \ArrayIterator($this->groups->toArray());
    }
}

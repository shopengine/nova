<?php

namespace ShopEngine\Nova\Structs\Navigation;

// @todo: on php8 use constructor property promotion
use Illuminate\Support\Collection;

final class NavigationStruct implements \IteratorAggregate
{
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

    public function getAvailableStruct(array $availableResources): NavigationStruct
    {
        return new NavigationStruct(
            $this->groups->map(fn (NavigationGroupStruct $group) => $group->getAvailableGroup($availableResources))->filter()->all()
        );
    }

    public function mergeWith(NavigationStruct $targetStruct): self
    {
        /** @var NavigationGroupStruct $structGroup */
        foreach ($targetStruct as $structGroup) {
            $key = $this->groups->search(fn (NavigationGroupStruct $item, $key) => $item->getTitle() === $structGroup->getTitle());

            if (is_numeric($key)) {
                $this->groups->get($key)->addItems($structGroup->getItems()->toArray());
            }
            else {
                $this->groups->add($structGroup);
            }
        }

        return $this;
    }

    public function sortGroups(array $titles): self
    {
        $this->groups = $this->groups->sortBy(
            fn(NavigationGroupStruct $navigationGroupStruct) => array_search($navigationGroupStruct->getTitle(), $titles)
        );

        return $this;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->groups->toArray());
    }
}

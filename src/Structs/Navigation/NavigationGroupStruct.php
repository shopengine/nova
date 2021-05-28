<?php

namespace Brainspin\Novashopengine\Structs\Navigation;

use Illuminate\Support\Collection;

// @todo: on php8 use constructor property promotion
final class NavigationGroupStruct {

    /**
     * @var string
     */
    private string $title;

    /**
     * @var bool
     */
    private bool $showTitle;

    private Collection $items;

    /**
     * NavigationStruct constructor.
     *
     * @param string $title
     * @param NavigationItemStruct[] $items
     * @param bool $showTitle
     */
    public function __construct(
        string $title,
        array $items,
        bool $showTitle = true
    ) {
        $this->title = $title;
        $this->showTitle = $showTitle;
        $this->items = collect($items);
    }

    /**
     * @return NavigationItemStruct[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return bool
     */
    public function showTitle(): bool
    {
        return $this->showTitle;
    }

    public function getAvailableGroup(array $availableResources) : ?NavigationGroupStruct
    {
        $items = $this->items->filter(
            fn( NavigationItemStruct $item) => in_array($item->getResourceClass(), $availableResources)
        );

        if ($items->isEmpty()) return null;

        return new NavigationGroupStruct(
            $this->title,
            $items->all(),
            $this->showTitle
        );
    }

    /**
     * @param string $title
     * @param array $plainStruct
     * @param bool $showTitle
     *
     * @return \Brainspin\Novashopengine\Structs\Navigation\NavigationGroupStruct
     */
    public static function create(
        string $title,
        array $plainStruct,
        bool $showTitle = true
    ) : NavigationGroupStruct {
        $items = [];
        foreach ($plainStruct as $plainItems) {
            $items[] = new NavigationItemStruct(
                $plainItems['title'],
                $plainItems['path'])
            ;
        }
        return new NavigationGroupStruct(
            $title,
            $items,
            $showTitle
        );
    }
}

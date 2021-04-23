<?php

namespace Brainspin\Novashopengine\Structs\Navigation;

use Brainspin\Novashopengine\Structs\Navigation\NavigationItemStruct;

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

    /**
     * @var NavigationItemStruct[]
     */
    private array $items;

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
        $this->items = $items;
    }

    /**
     * @return NavigationItemStruct[]
     */
    public function getItems(): array
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

<?php

namespace Brainspin\Novashopengine\Structs\Navigation;

// @todo: on php8 use constructor property promotion
final class NavigationItemStruct {

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $path;

    /**
     * NavigationStruct constructor.
     *
     * @param string $title
     * @param string $path
     */
    public function __construct(
        string $title,
        string $path
    ) {
        $this->title = $title;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

}

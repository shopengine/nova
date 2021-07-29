<?php

namespace ShopEngine\Nova\Structs\Navigation;

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
    private ?string $resourceClass;

    /**
     * NavigationStruct constructor.
     *
     * @param string $title
     * @param string $path
     */
    public function __construct(
        string $title,
        string $path,
        string $resourceClass = null
    ) {
        $this->title = $title;
        $this->path = $path;
        $this->resourceClass = $resourceClass;
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

    /**
     * @return string
     */
    public function getResourceClass(): ?string
    {
        return $this->resourceClass;
    }
}

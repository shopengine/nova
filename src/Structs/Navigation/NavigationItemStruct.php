<?php

namespace ShopEngine\Nova\Structs\Navigation;

// @todo[php8] on php8 use constructor property promotion
final class NavigationItemStruct
{
    private string $title;
    private string $path;
    private ?string $resourceClass;
    private int $order;

    public function __construct(
        string $title,
        string $path,
        string $resourceClass = null,
        int $order = 999
    ) {
        $this->title = $title;
        $this->path = $path;
        $this->resourceClass = $resourceClass;
        $this->order = $order;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getResourceClass(): ?string
    {
        return $this->resourceClass;
    }

    public function getOrder(): int
    {
        return $this->order;
    }
}

<?php

namespace Brainspin\Novashopengine\Structs\Navigation;

// @todo: on php8 use constructor property promotion
final class NavigationStruct {

    private array $groups;

    /**
     * NavigationStruct constructor.
     *
     * @param array $groups
     */
    public function __construct(
        array $groups
    ) {
        $this->groups = $groups;
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

}

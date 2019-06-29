<?php

namespace Report\Property;


interface OnOnePageInterface
{
    /**
     * @return bool
     */
    public function isOnOnePage(): bool;

    /**
     * @param bool $isOnOnePage
     * @return $this
     */
    public function setIsOnOnePage(bool $isOnOnePage);
}

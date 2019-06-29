<?php

namespace Report\Property;


trait OnOnePage
{
    protected $isOnOnePage = false;

    /**
     * @return bool
     */
    public function isOnOnePage(): bool
    {
        return $this->isOnOnePage;
    }

    /**
     * @param bool $isOnOnePage
     * @return $this
     */
    public function setIsOnOnePage(bool $isOnOnePage)
    {
        $this->isOnOnePage = $isOnOnePage;
        return $this;
    }
}

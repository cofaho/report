<?php

namespace Report\Band\Property;


interface PageBreakAfterInterface
{
    /**
     * @return bool
     */
    public function isPageBreakAfter(): bool;

    /**
     * @param bool $pageBreakAfter
     * @return $this
     */
    public function setPageBreakAfter(bool $pageBreakAfter);
}

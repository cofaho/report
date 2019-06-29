<?php

namespace Report\Band\Property;


interface PageBreakBeforeInterface
{
    /**
     * @return bool
     */
    public function isPageBreakBefore(): bool;

    /**
     * @param bool $pageBreakBefore
     * @return $this
     */
    public function setPageBreakBefore(bool $pageBreakBefore);
}

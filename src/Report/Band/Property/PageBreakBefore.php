<?php

namespace Report\Band\Property;


trait PageBreakBefore
{
    protected $pageBreakBefore = false;

    /**
     * @return bool
     */
    public function isPageBreakBefore(): bool
    {
        return $this->pageBreakBefore;
    }

    /**
     * @param bool $pageBreakBefore
     * @return $this
     */
    public function setPageBreakBefore(bool $pageBreakBefore)
    {
        $this->pageBreakBefore = $pageBreakBefore;
        return $this;
    }
}

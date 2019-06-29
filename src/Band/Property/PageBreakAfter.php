<?php

namespace Report\Band\Property;


trait PageBreakAfter
{
    protected $pageBreakAfter = false;

    /**
     * @return bool
     */
    public function isPageBreakAfter(): bool
    {
        return $this->pageBreakAfter;
    }

    /**
     * @param bool $pageBreakAfter
     * @return $this
     */
    public function setPageBreakAfter(bool $pageBreakAfter)
    {
        $this->pageBreakAfter = $pageBreakAfter;
        return $this;
    }
}

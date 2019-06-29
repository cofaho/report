<?php

namespace Report\Band\Property;


trait PrintOnEveryPage
{
    /**
     * @var bool
     */
    protected $printOnEveryPage = false;

    /**
     * @return bool
     */
    public function isPrintedOnEveryPage(): bool
    {
        return $this->printOnEveryPage;
    }

    /**
     * @param bool $printOnEveryPage
     * @return $this
     */
    public function setPrintOnEveryPage(bool $printOnEveryPage)
    {
        $this->printOnEveryPage = $printOnEveryPage;
        return $this;
    }
}

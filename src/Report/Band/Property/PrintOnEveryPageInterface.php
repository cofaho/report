<?php

namespace Report\Band\Property;


interface PrintOnEveryPageInterface
{
    /**
     * @return bool
     */
    public function isPrintedOnEveryPage(): bool;

    /**
     * @param bool $printOnEveryPage
     * @return $this
     */
    public function setPrintOnEveryPage(bool $printOnEveryPage);
}

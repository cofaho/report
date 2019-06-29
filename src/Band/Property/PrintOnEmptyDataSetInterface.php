<?php

namespace Report\Band\Property;


interface PrintOnEmptyDataSetInterface
{
    /**
     * @return bool
     */
    public function isPrintedOnEmptyDataSet(): bool;

    /**
     * @param bool $printOnEmptyDataSet
     * @return $this
     */
    public function setPrintOnEmptyDataSet(bool $printOnEmptyDataSet);
}

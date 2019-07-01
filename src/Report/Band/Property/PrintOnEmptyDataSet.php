<?php

namespace Report\Band\Property;


trait PrintOnEmptyDataSet
{
    /**
     * @var bool
     */
    protected $printOnEmptyDataSet = false;

    /**
     * @return bool
     */
    public function isPrintedOnEmptyDataSet(): bool
    {
        return $this->printOnEmptyDataSet;
    }

    /**
     * @param bool $printOnEmptyDataSet
     * @return $this
     */
    public function setPrintOnEmptyDataSet(bool $printOnEmptyDataSet)
    {
        $this->printOnEmptyDataSet = $printOnEmptyDataSet;
        return $this;
    }
}

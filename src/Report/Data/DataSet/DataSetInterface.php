<?php

namespace Report\Data\DataSet;


use Report\Event\DispatcherInterface;
use Report\Property\NameInterface;

interface DataSetInterface extends NameInterface, DispatcherInterface
{
    public function open();
    public function close();
    public function rewind();
    public function current();
    public function next();
    public function prev();
    public function key();
    public function valid();
    public function isActive(): bool;
    public function field(string $fieldName);
    public function hasRows();
    public function setMasterLink($detailField, DataSetInterface $masterDataSet, $masterField);
}

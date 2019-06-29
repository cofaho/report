<?php

namespace Report\Data\DataSet;


use Report\Data\MasterLink;

class ArrayDataSet extends AbstractDataSet
{
    public function __construct(array $data, $name = null)
    {
        $this->data = $data;
        parent::__construct($name);
    }

    /**
     * @param string $fieldName
     * @return mixed|null
     */
    public function field(string $fieldName)
    {
        return isset($this->data[$this->position])
        && isset($this->data[$this->position][$fieldName]) ? $this->data[$this->position][$fieldName] : null;
    }

    public function hasRows()
    {
        $has = parent::hasRows();
        if ($has && $this->masterLink !== null) {
            $has = $this->isValidLink();
        }
        return $has;
    }

    public function close()
    {
        $this->isActive = false;
    }

    public function rewind()
    {
        parent::rewind();

        if ($this->masterLink === null) {
            return;
        }

        if (is_array($this->masterLink)) {
            $c = count($this->data);
            $m = count($this->masterLink);
            $i = 0;
            $prevPosition = $this->position;
            while ($i < $m && $this->position < $c) {
                $this->nextLinkedPosition($this->masterLink[$i]);
                if ($prevPosition === $this->position) {
                    ++$i;
                } else {
                    $i = 0;
                    $prevPosition = $this->position;
                }
            }
        } else {
            $this->nextLinkedPosition($this->masterLink);
        }
    }

    protected function nextLinkedPosition(MasterLink $link)
    {
        $c = count($this->data);
        $masterFKValue = $link->getMasterFieldValue();
        $detailPKValue = null;
        $detailFieldName = $link->getDetailField();
        while ($this->position < $c) {
            $detailPKValue = $this->data[$this->position][$detailFieldName];
            if ($masterFKValue == $detailPKValue) {
                break;
            }
            ++$this->position;
        }
    }

    public function current()
    {
        return $this->data[$this->position];
    }

    public function valid()
    {
        $valid = isset($this->data[$this->position]);
        if ($valid && $this->masterLink !== null) {
            $valid = $this->isValidLink();
        }
        if ($valid) {
            $this->onRowChanged();
        }
        return $valid;
    }

    protected function isValidLink()
    {
        $masterFKValue = $this->masterLink->getMasterFieldValue();
        $detailFieldName = $this->masterLink->getDetailField();
        $detailPKValue = $this->data[$this->position][$detailFieldName];
        return $masterFKValue == $detailPKValue;
    }

}

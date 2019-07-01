<?php

namespace Report\Data\DataSet;


use Iterator;
use Report\Data\Event\RowChanged;
use Report\Data\MasterLink;
use Report\Event\Dispatcher;
use Report\Property\Name;

abstract class AbstractDataSet extends Dispatcher implements DataSetInterface, Iterator
{
    use Name;
    /**
     * @var null|array
     */
    protected $data = null;
    /**
     * @var int
     */
    protected $position = 0;
    /**
     * @var bool
     */
    protected $isActive = false;
    /**
     * @var null|MasterLink|MasterLink[]
     */
    protected $masterLink = null;

    /**
     * AbstractDataSet constructor.
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        if ($name !== null) {
            $this->setName($name);
        }
    }

    /**
     * @param string $fieldName
     * @return mixed|null
     */
    public function __get(string $fieldName)
    {
        return $this->field($fieldName);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param string $fieldName
     * @return mixed|null
     */
    public function field(string $fieldName)
    {
        return isset($this->data) && isset($this->data[$fieldName]) ? $this->data[$fieldName] : null;
    }

    public function hasRows()
    {
        return !empty($this->data);
    }

    public function open()
    {
        if ($this->isActive) {
            return;
        }
        $this->rewind();
        $this->isActive = true;
    }

    public function close()
    {
        unset($this->data);
        $this->data = null;
        $this->isActive = false;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->data;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function prev()
    {
        --$this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $valid = isset($this->data);
        if ($valid) {
            $this->onRowChanged();
        }
        return $valid;
    }

    /**
     * @param string|string[] $detailField
     * @param DataSetInterface $masterDataSet
     * @param string|string[] $masterField
     * @return $this
     */
    public function setMasterLink($detailField, DataSetInterface $masterDataSet, $masterField)
    {
        if (is_array($detailField)) {
            $this->masterLink = [];
            foreach ($detailField as $i => $field) {
                $this->masterLink[] = new MasterLink($field, $masterDataSet, $masterField[$i]);
            }
        } else {
            $this->masterLink = new MasterLink($detailField, $masterDataSet, $masterField);
        }

        $masterDataSet->attachListener(RowChanged::getName(), [$this, 'onMasterRowChanged']);
        return $this;
    }

    public function onRowChanged()
    {
        $event = new RowChanged();
        $this->dispatch($event);
    }

    public function onMasterRowChanged()
    {
        $this->close();
        $this->open();
    }

}

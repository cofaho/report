<?php

namespace Report\Renderer;


use Report\Band\BandInterface;
use Report\Element\ElementInterface;

class Scope
{
    /**
     * @var null|ElementInterface|BandInterface
     */
    public $target = null;

    public $pageNumber = 0;

    public $totalPages = '';

    public function getExpressionResult($expression)
    {
        //TODO: expressions and functions support
        $parts = explode('.', trim($expression, '[] '));
        $i = 0;
        $result = $this;
        while ($i < count($parts)) {
            $result = $result->{$parts[$i]};
            if ($result === null) {
                break;
            }
            ++$i;
        }
        return $result;
    }

    /**
     * @return int|null
     */
    public function getRowNumber()
    {
        $target = $this->target;
        if ($target === null) {
            return null;
        }

        if ($target instanceof ElementInterface) {
            $target = $target->getParent();
        }
        if (!($target instanceof BandInterface)) {
            return null;
        }

        $ds = $target->getDataSource();

        return $ds === null ? null : $ds->key();
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function registerVariable($name, $value)
    {
        $this->$name = $value;
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function deleteVariable($name)
    {
        if (isset($this->$name)) {
            unset($this->$name);
        }

        return $this;
    }
}

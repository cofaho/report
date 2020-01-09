<?php

namespace Report\Band;


use Report\AbstractBandOwner;
use Report\BandContainerInterface;
use Report\Data\DataSet\DataSetInterface;
use Report\Element\ElementInterface;
use Report\Property\Name;
use Serializable;

/**
 * Class AbstractBand
 * @package Report\Band
 * @property bool $isTail
 */
abstract class AbstractBand extends AbstractBandOwner implements BandInterface, Serializable
{
    use Name;
	/**
	 * @var null|ElementInterface[]
	 */
	protected $elements = null;
    /**
     * @var null|DataSetInterface
     */
    protected $dataSource = null;
    /**
     * Actual band height
     * @var float
     */
    protected $height = null;
    /**
     * Minimal band height
     * @var float
     */
    protected $minHeight = null;
    /**
     * @var BandContainerInterface
     */
    protected $parent;

    /**
     * @param BandContainerInterface $container
     */
    public function __construct(BandContainerInterface $container)
    {
        $this->parent = $container;
        $container->addBand($this);
    }

    public function __clone()
    {
        if (empty($this->elements)) {
            return;
        }

        $elements = $this->getElements();
        $this->setElements([]);
        $this->isTail = true;

        foreach ($elements as $element) {
            $this->addElement(clone $element);
        }
    }

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize([
            $this->name,
            $this->bands,
            $this->elements,
            $this->dataSource,
            $this->height,
            $this->parent
        ]);
    }

    /**
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        list(
            $this->name,
            $this->bands,
            $this->elements,
            $this->dataSource,
            $this->height,
            $this->parent
        ) = unserialize($serialized);
    }

    /**
     * @param $dataSet
     * @return $this
     */
    public function setDataSource(DataSetInterface $dataSet)
    {
        $this->dataSource = $dataSet;
        return $this;
    }

    /**
     * @return DataSetInterface|null
     */
    public function getDataSource(): ?DataSetInterface
    {
        return $this->dataSource;
    }

    /**
     * @param ElementInterface $element
     * @return $this
     */
    public function addElement(ElementInterface $element)
    {
        $this->elements[] = $element;
        $element->setParent($this);
        return $this;
    }

    /**
     * @param null|ElementInterface[] $elements
     * @return $this
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * @return null|ElementInterface[]
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param float|null $height
     * @return $this
     */
    public function setMinHeight(?float $height)
    {
        $this->minHeight = $height;
        return $this;
    }

    /**
     * @return float
     */
    public function getMinHeight(): float
    {
        if ($this->minHeight === null) {
            $this->minHeight = 0;
            if (!empty($this->elements)) {
                foreach ($this->elements as $element) {
                    $this->minHeight = max($this->minHeight, $element->getY() + $element->getMinHeight());
                }
            }
        }
        return $this->minHeight;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        if ($this->height === null) {
            $this->height = 0;
            if (!empty($this->elements)) {
                foreach ($this->elements as $element) {
                    $this->height = max($this->height, $element->getMaxY());
                }
            }
        }
        return $this->height;
    }

    /**
     * @param float|null $height
     * @return $this
     */
    public function setHeight(?float $height): BandInterface
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return BandContainerInterface
     */
    public function getParent(): BandContainerInterface
    {
        return $this->parent;
    }

    /**
     * @param BandContainerInterface $parent
     * @return $this
     */
    public function setParent(BandContainerInterface $parent)
    {
        $this->parent = $parent;
        return $this;
    }

}

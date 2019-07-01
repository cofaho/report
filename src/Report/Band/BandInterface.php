<?php

namespace Report\Band;


use Report\BandContainerInterface;
use Report\Data\DataSet\DataSetInterface;
use Report\Element\ElementInterface;
use Report\Property\NameInterface;

interface BandInterface extends NameInterface
{
    /**
     * @param BandInterface $band
     * @return BandInterface
     */
    public function addBand(BandInterface $band);

    /**
     * @param string $type
     * @return BandInterface|null
     */
    public function getBand($type);

    /**
     * @param null|string $type
     * @return null|BandInterface[]
     */
    public function getBands($type = null);

    /**
     * @param DataSetInterface $dataSet
     * @return BandInterface
     */
    public function setDataSource(DataSetInterface $dataSet);

    /**
     * @return null|DataSetInterface
     */
    public function getDataSource(): ?DataSetInterface;

    /**
     * @param ElementInterface $element
     * @return BandInterface
     */
    public function addElement(ElementInterface $element);

    /**
     * @param ElementInterface[] $elements
     * @return BandInterface
     */
    public function setElements($elements);
    /**
     * @return null|ElementInterface[]
     */
    public function getElements();

    /**
     * @param float|null $height
     * @return BandInterface
     */
    public function setMinHeight(?float $height);

    /**
     * @return float
     */
    public function getMinHeight(): float;
    /**
     * @param float|null $height
     * @return BandInterface
     */
    public function setHeight(?float $height);

    /**
     * @return float
     */
    public function getHeight(): float;

    /**
     * @param BandContainerInterface $container
     * @return BandInterface
     */
    public function setParent(BandContainerInterface $container);

    /**
     * @return BandContainerInterface
     */
    public function getParent(): BandContainerInterface;

}

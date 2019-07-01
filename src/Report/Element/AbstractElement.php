<?php

namespace Report\Element;


use Report\Band\BandInterface;
use Report\Helper\Rectangle;
use Report\Property\Name;
use Report\Property\OnOnePage;
use Serializable;

abstract class AbstractElement implements ElementInterface, Serializable
{
    use OnOnePage, Name;

    /**
     * @var float
     */
    protected $x = 0;
    /**
     * @var float
     */
    public $dx = 0;
    /**
     * @var float
     */
    protected $y = 0;
    /**
     * @var float
     */
    protected $dy = 0;
    /**
     * @var float
     */
    protected $rotation = 0;
    /**
     * @var null|Rectangle|Rectangle[]
     */
    protected $bbox = null;
    /**
     * @var BandInterface
     */
    protected $band;

    /**
     * Element constructor.
     * @param BandInterface $band
     */
    public function __construct(BandInterface $band)
    {
        $this->band = $band;
        $band->addElement($this);
    }

    /**
     * @param float $x
     * @return $this
     */
    public function setX($x)
    {
        $this->x = $x;
        $this->bbox = null;
        return $this;
    }

    /**
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * @param float $y
     * @return $this
     */
    public function setY($y)
    {
        $this->y = $y;
        $this->bbox = null;
        return $this;
    }

    /**
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }

    /**
     * @param float $x
     * @param float $y
     * @return $this
     */
    public function setXY($x, $y)
    {
        $this->setX($x);
        $this->setY($y);
        return $this;
    }

    /**
     * @return float
     */
    public function getDx(): float
    {
        if (!$this->bbox) {
            $this->getBBox();
        }
        return $this->dx;
    }

    /**
     * @return float
     */
    public function getDy(): float
    {
        if (!$this->bbox) {
            $this->getBBox();
        }
        return $this->dy;
    }

    /**
     * @return float
     */
    public function getRotation(): float
    {
        return $this->rotation;
    }

    /**
     * @param float $rotation
     * @return $this
     */
    public function setRotation(float $rotation)
    {
        $this->rotation = $rotation % 360;
        $this->bbox = null;
        return $this;
    }

    /**
     * @param BandInterface $band
     * @return AbstractElement
     */
    public function setParent(BandInterface $band)
    {
        $this->band = $band;
        return $this;
    }

    /**
     * @return BandInterface
     */
    public function getParent(): BandInterface
    {
        return $this->band;
    }

}

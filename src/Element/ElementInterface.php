<?php

namespace Report\Element;


use Report\Band\BandInterface;
use Report\Helper\Rectangle;
use Report\Property\NameInterface;

interface ElementInterface extends NameInterface
{
    /**
     * @param float $x
     * @return $this
     */
    public function setX(float $x);
    /**
     * @return float
     */
    public function getX(): float;
    /**
     * @return float
     */
    public function getDx(): float;
    /**
     * @param float $y
     * @return $this
     */
    public function setY(float $y);
    /**
     * @return float
     */
    public function getY(): float;
    /**
     * @return float
     */
    public function getDy(): float;
    /**
     * @return float
     */
    public function getMaxY(): float;
    /**
     * @param float $x
     * @param float $y
     * @return $this
     */
    public function setXY(float $x, float $y);
    /**
     * @return Rectangle
     */
    public function getBBox(): Rectangle;
    /**
     * @param float|null $width
     * @return $this
     */
    public function setWidth(?float $width);
    /**
     * @return float|null
     */
    public function getWidth(): ?float;
    /**
     * @return float
     */
    public function getMinWidth(): ?float;
    /**
     * @param float|null $width
     * @return $this
     */
    public function setRenderWidth(?float $width);
    /**
     * @return float
     */
    public function getRenderWidth(): float;
    /**
     * @param float|null $height
     * @return $this
     */
    public function setHeight(?float $height);
    /**
     * @return float|null
     */
    public function getHeight(): ?float;
    /**
     * @return float
     */
    public function getMinHeight(): ?float;
    /**
     * @param float|null $height
     * @return $this
     */
    public function setRenderHeight(?float $height);
    /**
     * @return float
     */
    public function getRenderHeight(): float;
    /**
     * @param float $rotation
     * @return $this
     */
    public function setRotation(float $rotation);
    /**
     * @return float
     */
    public function getRotation(): float;
    /**
     * @param bool $onOnePage
     * @return $this
     */
    public function setIsOnOnePage(bool $onOnePage);
    /**
     * @return bool
     */
    public function isOnOnePage(): bool;
    /**
     * @param BandInterface $band
     * @return $this
     */
    public function setParent(BandInterface $band);
    /**
     * @return BandInterface
     */
    public function getParent(): BandInterface;
}

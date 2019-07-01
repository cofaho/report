<?php

namespace Report;


use Report\Band\BandInterface;

interface BandContainerInterface
{
    /**
     * @return float
     */
    public function getWidth(): ?float;

    /**
     * @param float $width
     * @return $this
     */
    public function setWidth(?float $width);

    /**
     * @param int|null $side
     * @return mixed
     */
    public function getMargin(int $side = null);

    /**
     * @param float $margin
     * @param int|null $side
     * @return $this
     */
    public function setMargin(float $margin, int $side = null);

    /**
     * @return float|null
     */
    public function getFreeHeight(): ?float;

    /**
     * @param float $freeHeight
     * @return $this
     */
    public function setFreeHeight(float $freeHeight);

    /**
     * @param float $freeHeight
     * @return $this
     */
    public function subFreeHeight(float $freeHeight);

    /**
     * @param BandInterface $band
     * @return $this
     */
    public function addBand(BandInterface $band);

    /**
     * @param null|string $type
     * @return null|BandInterface[]
     */
    public function getBands($type = null);

    /**
     * @param string $type
     * @return BandInterface|null
     */
    public function getBand($type);
}

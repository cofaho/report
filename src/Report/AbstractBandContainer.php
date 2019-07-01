<?php

namespace Report;


abstract class AbstractBandContainer extends AbstractBandOwner implements BandContainerInterface
{
    /**
     * @var float
     */
    protected $width = 0;
    /**
     * @var float[]
     */
    protected $margin = [0, 0, 0, 0];
    /**
     * @var float
     */
    protected $freeHeight;

    /**
     * @return float
     */
    public function getWidth(): ?float
    {
        return $this->width;
    }

    /**
     * @param float $width
     * @return $this
     */
    public function setWidth(?float $width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @param int|null $side
     * @return mixed
     */
    public function getMargin(int $side = null)
    {
        return $side === null ? $this->margin : $this->margin[$side];
    }

    /**
     * @param float $margin
     * @param int|null $side
     * @return $this
     */
    public function setMargin(float $margin, int $side = null)
    {
        if ($side !== null) {
            $this->margin[$side] = $margin;
            return $this;
        }
        for ($i = 0; $i < 4; ++$i) {
            $this->margin[$i] = $margin;
        }
        return $this;
    }

    /**
     * @return float|null
     */
    public function getFreeHeight(): ?float
    {
        return $this->freeHeight;
    }

    /**
     * @param float $freeHeight
     * @return $this
     */
    public function setFreeHeight(float $freeHeight)
    {
        $this->freeHeight = $freeHeight;
        return $this;
    }

    /**
     * @param float $freeHeight
     * @return $this
     */
    public function subFreeHeight(float $freeHeight)
    {
        $this->freeHeight -= $freeHeight;
        return $this;
    }

}

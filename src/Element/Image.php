<?php

namespace Report\Element;


use Report\Helper\Rectangle;

class Image extends Element
{
    const FIT_AUTO = 0;

    const FIT_KEEP_RATIO = 1;

    const FIT_STRETCH = 2;
    /**
     * @var string|null
     */
    protected $src = null;
    /**
     * @var int
     */
    protected $fit = 0;
    /**
     * @var int|null
     */
    protected $imageSize = null;

    /**
     * @var float|null
     */
    protected $width = null;
    /**
     * @var float|null
     */
    protected $renderWidth = null;
    /**
     * @var float|null
     */
    protected $height = null;
    /**
     * @var float|null
     */
    protected $renderHeight = null;

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize([
            $this->isOnOnePage,
            $this->name,
            $this->src,
            $this->fit,
            $this->x,
            $this->y,
            $this->width,
            $this->height,
            $this->imageSize,
            $this->rotation,
            $this->band
        ]);
    }

    /**
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        list(
            $this->isOnOnePage,
            $this->name,
            $this->src,
            $this->fit,
            $this->x,
            $this->y,
            $this->width,
            $this->height,
            $this->imageSize,
            $this->rotation,
            $this->band
        ) = unserialize($serialized);
    }

    /**
     * @return float
     */
    public function getMaxY(): float
    {
        return $this->getY() + $this->getRenderHeight();
    }

    public function getImageBBox()
    {
        $bbox = new Rectangle([
            'x' => $this->getX(),
            'y' => $this->getY(),
            'width' => $this->imageSize[0],
            'height' => $this->imageSize[1]
        ]);

        $w = $this->getWidth();
        $h = $this->getHeight();
        if ($w !== null || $h !== null) {
            switch ($this->getFit()) {
                case Image::FIT_KEEP_RATIO:
                    if ($w !== null && $h === null) {
                        $bbox->height = $h * $bbox->width / $w;
                        $bbox->width = $w;
                    } elseif ($w === null && $h !== null) {
                        $bbox->width = $w * $bbox->height / $h;
                        $bbox->height = $h;
                    } else {
                        $whReal = $bbox->width / $bbox->height;
                        $whSet = $w / $h;
                        if ($whSet > $whReal) {
                            $bbox->width = $h * $whReal;
                            $bbox->height = $h;
                        } else {
                            $bbox->width = $w;
                            $bbox->height = $w / $whReal;
                        }
                    }
                    break;
                case Image::FIT_STRETCH:
                    $bbox->width = $w;
                    $bbox->height = $h;
                    break;
            }
        }

        return $bbox;

    }

    /**
     * @return Rectangle
     */
    public function getBBox(): Rectangle
    {
        if ($this->bbox !== null) {
            return clone $this->bbox;
        }

        $bbox = $this->getImageBBox();

        if ($alfa = $this->getRotation()) {
            $alfa = $alfa * M_PI / 180;
            $sin = abs(sin($alfa));
            $cos = abs(cos($alfa));
            $w = $bbox->width * $cos + $bbox->height * $sin;
            $h = $bbox->width * $sin + $bbox->height * $cos;
            $this->dx = ($w - $bbox->width) / 2;
            $this->dy = ($h - $bbox->height) / 2;
            $bbox->width = $w;
            $bbox->height = $h;
        } else {
            $this->dx = 0;
            $this->dy = 0;
        }

        $bbox->width = max($bbox->width, $this->renderWidth);
        $bbox->height = max($bbox->height, $this->renderHeight);

        $this->bbox = $bbox;

        return clone $bbox;
    }

    /**
     * @param float|null $width
     * @return $this
     */
    public function setWidth(?float $width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getWidth(): ?float
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getMinWidth(): ?float
    {
        return 0;
    }

    /**
     * @param float|null $width
     * @return $this
     */
    public function setRenderWidth(?float $width)
    {
        $this->renderWidth = $width;
        return $this;
    }

    /**
     * @return float
     */
    public function getRenderWidth(): float
    {
        return $this->renderWidth ?: $this->getBBox()->width;
    }

    /**
     * @param float|null $height
     * @return $this
     */
    public function setHeight(?float $height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHeight(): ?float
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getMinHeight(): ?float
    {
        return 0;
    }

    /**
     * @param float|null $height
     * @return $this
     */
    public function setRenderHeight(?float $height)
    {
        $this->renderHeight = $height;
        return $this;
    }

    /**
     * @return float
     */
    public function getRenderHeight(): float
    {
        return $this->renderHeight ?: $this->getBBox()->height;
    }

    /**
     * @return string|null
     */
    public function getSrc(): ?string
    {
        return $this->src;
    }

    /**
     * @param string|null $src
     * @return $this
     */
    public function setSrc(?string $src)
    {
        $this->src = $src;
        $this->imageSize = getimagesize($src);
        return $this;
    }

    /**
     * @return int
     */
    public function getFit(): int
    {
        return $this->fit;
    }

    /**
     * @param int $fit
     * @return $this
     */
    public function setFit(int $fit)
    {
        $this->fit = $fit;
        return $this;
    }



}

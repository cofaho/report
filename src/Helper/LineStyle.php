<?php

namespace Report\Helper;


class LineStyle
{
    const SOLID = 1;
    const DASHED = 2;
    const DOTTED = 3;
    /**
     * @var int
     */
    protected $style = LineStyle::SOLID;
    /**
     * @var float
     */
    protected $width = 0;
    /**
     * @var Color
     */
    protected $color;

    public function __construct(Color $color = null, $width = 0.05, $style = LineStyle::SOLID )
    {
        $this->style = $style;
        $this->width = $width;
        $this->color = $color ?? new Color();
    }

    /**
     * @param Color $color
     * @return $this
     */
    public function setColor(Color $color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param int $style
     * @return $this
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * @return int
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param float $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }


}

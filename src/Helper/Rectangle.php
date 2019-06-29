<?php

namespace Report\Helper;


class Rectangle
{
    public $x = 0;
    public $y = 0;
    public $width = null;
    public $height = null;

    public function __construct(array $rectangle = null)
    {
        if ($rectangle === null) {
            return;
        }
        $this->x = $rectangle['x'] ?? 0;
        $this->y = $rectangle['y'] ?? 0;
        $this->width = $rectangle['width'] ?? null;
        $this->height = $rectangle['height'] ?? null;
    }

    public function toArray()
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height
        ];
    }
}

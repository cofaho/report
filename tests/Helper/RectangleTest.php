<?php

namespace Report\Helper;


use PHPUnit\Framework\TestCase;

class RectangleTest extends TestCase
{
    public function testRectangle()
    {
        $r = new Rectangle(['x' => 1, 'y' => 2, 'width' => 3, 'height' => 4]);
        self::assertEquals(1, $r->x);
        self::assertEquals(2, $r->y);
        self::assertEquals(3, $r->width);
        self::assertEquals(4, $r->height);
    }
}

<?php

namespace Report\Helper;

use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{

    public function test__construct()
    {
        $color = new Color('#01020F');
        self::assertEquals(15, $color->getB());

        $color = new Color('#01020FFF');
        self::assertEquals(1, $color->getR());
        self::assertEquals(100, $color->getAlfa());

        $color = new Color(1, 2, 3);
        self::assertEquals(3, $color->getB());

        $color = new Color(1, 2, 3, 30);
        self::assertEquals(2, $color->getG());
        self::assertEquals(30, $color->getAlfa());
    }
}

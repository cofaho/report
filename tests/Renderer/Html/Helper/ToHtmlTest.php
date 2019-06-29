<?php namespace report\Renderer\Html\Helper;

use PHPUnit\Framework\TestCase;
use Report\Helper\Color;
use Report\Helper\LineStyle;

class ToHtmlTest extends TestCase
{
    public function testColor()
    {
        $color = new Color(1, 2, 15);
        self::assertEquals('#01020F', ToHtml::Color($color));
        $color->setAlfa(5);
        self::assertEquals('#01020F0D', ToHtml::Color($color));
    }

    public function testLine()
    {
        $color = new Color(1, 2, 15);
        $lineStyle = new LineStyle($color, 1, LineStyle::DASHED);
        self::assertEquals('dashed 1px #01020F', ToHtml::Line($lineStyle));
    }
}

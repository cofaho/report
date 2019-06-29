<?php

namespace Report\Renderer\Helper;


use PHPUnit\Framework\TestCase;
use Report\Helper\FontStyle;

class TextTest extends TestCase
{
    protected $s = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    protected $delta = 0.05;

    protected function setUp(): void
    {
        FontData::load('Ubuntu', './tests/_assets/Ubuntu-R.ttf');
    }

    public function testTextSize()
    {
        $bbox = Text::getTextSize($this->s);
        self::assertEqualsWithDelta(['x' => 0, 'y' => 0, 'width' => 767.2, 'height' => 16.8], $bbox->toArray(), $this->delta);
    }

    public function testWordWrap()
    {
        $bbox = Text::getTextSize($this->s, null, 100);
        self::assertEqualsWithDelta(['x' => 0, 'y' => 0, 'width' => 99.24, 'height' => 151.2], $bbox->toArray(), $this->delta);
    }

    public function testLetterSpacing()
    {
        $style = new FontStyle([
            'letter-spacing' => 2
        ]);
        $bbox = Text::getTextSize($this->s, $style);
        self::assertEqualsWithDelta(['x' => 0, 'y' => 0, 'width' => 1013.2, 'height' => 16.8], $bbox->toArray(), $this->delta);
    }

    public function testWordSpacing()
    {
        $style = new FontStyle([
            'word-spacing' => 2
        ]);
        $bbox = Text::getTextSize($this->s, $style);
        self::assertEqualsWithDelta(['x' => 0, 'y' => 0, 'width' => 803.2, 'height' => 16.8], $bbox->toArray(), $this->delta);
    }

    public function testStrlen()
    {
        $style = new FontStyle();
        $l = Text::strlenInRect($this->s, $style, 150, 15);
        self::assertEquals(0, $l);
        $l = Text::strlenInRect($this->s, $style, 150, 20);
        self::assertEquals(21, $l);
        $l = Text::strlenInRect($this->s, $style, 150, 55);
        self::assertEquals(63, $l);
        $l = Text::strlenInRect($this->s, $style, 150, 85);
        self::assertEquals(102, $l);
        $l = Text::strlenInRect($this->s, $style, 150, 105);
        self::assertEquals(123, $l);

        $l = Text::strlenInRect($this->s, $style, 150, 55, false);
        self::assertEquals(70, $l);
        $l = Text::strlenInRect($this->s, $style, 150, 70, false);
        self::assertEquals(94, $l);
    }

    public function testUndefinedSymbols()
    {
        $bbox = Text::getTextSize('σႴ♘');
        self::assertEquals(['x' => 0, 'y' => 0, 'width' => 20.34375, 'height' => 16.8], $bbox->toArray());
    }
}

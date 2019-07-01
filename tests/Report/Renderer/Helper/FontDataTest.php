<?php

namespace Report\Renderer\Helper;


use PHPUnit\Framework\TestCase;

class FontDataTest extends TestCase
{
    public function testSetState()
    {
        $charMap = [1 => 1, 2 => 2];
        $widths = [100, 200];
        $kernTree = [1 => [2 => 10]];
        $fontData = FontData::__set_state([
            'name' => 'fontName',
            'charMap' => $charMap,
            'widths' => $widths,
            'kernTree' => $kernTree,
            'unitsPerEm' => 1000
        ]);

        self::assertEquals('fontName', $fontData->name);
        self::assertEquals($charMap, $fontData->charMap);
        self::assertEquals($widths, $fontData->widths);
        self::assertEquals($kernTree, $fontData->kernTree);
        self::assertEquals(1000, $fontData->unitsPerEm);
    }

    public function testAdd()
    {
        $fontData = new FontData();
        $fontData->name = 'fontName';
        FontData::add($fontData);
        $fonts = FontData::getFonts();
        self::assertArrayHasKey('fontName', $fonts);
    }

    public function testLoad()
    {
        $fontData = FontData::load('Ubuntu', './tests/_assets/Ubuntu-R.ttf');
        self::assertEquals($fontData->name, 'Ubuntu');
    }

    public function testLoadNonExistentFont()
    {
        self::expectException('FontLib\Exception\FontNotFoundException');
        FontData::load('noFont', 'noFont.ttf');
    }
}

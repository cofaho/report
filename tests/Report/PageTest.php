<?php


use Report\Page;
use PHPUnit\Framework\TestCase;
use Report\Report;

class PageTest extends TestCase
{

    public function testGetFormatInUnits()
    {
        $format = Page::getFormatInUnits(Page::FORMAT_A4, Report::UNITS_PX);
        self::assertEquals(794, $format[0]);
        $format = Page::getFormatInUnits(Page::FORMAT_A4, Report::UNITS_PT);
        self::assertEqualsWithDelta(595, $format[0], 0.5);
        $format = Page::getFormatInUnits(Page::FORMAT_A4, Report::UNITS_CM);
        self::assertEquals(21, $format[0]);
        $format = Page::getFormatInUnits(Page::FORMAT_A4, Report::UNITS_MM);
        self::assertEquals(210, $format[0]);
        $format = Page::getFormatInUnits(Page::FORMAT_A4, Report::UNITS_IN);
        self::assertEquals(8.267721, $format[0]);
    }
}

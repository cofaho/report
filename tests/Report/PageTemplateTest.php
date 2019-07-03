<?php

use PHPUnit\Framework\TestCase;
use Report\Band\DataBand;
use Report\Page;
use Report\PageTemplate;
use Report\Report;

class PageTemplateTest extends TestCase
{
    public function testSetMargin()
    {
        $p = new PageTemplate();
        $p->setMargin(2);

        self::assertEquals(2, $p->getMargin(Page::MARGIN_BOTTOM));
    }

    public function testAddBand()
    {
        $page = new PageTemplate();
        $band = new DataBand($page);
        $p = new PageTemplate();
        $p->addBand($band);

        self::assertEquals($band, $p->getBand(DataBand::class));
    }

    public function testSetFormat()
    {
        $report = new Report();
        $p = new PageTemplate($report);
        $p->setFormat(Page::FORMAT_A4);

        self::assertEquals(Page::DIMENSIONS[Page::FORMAT_A4][0], $p->getWidth());
        self::assertEquals(Page::DIMENSIONS[Page::FORMAT_A4][1], $p->getHeight());
    }

    public function testSetOrientation()
    {
        $report = new Report();
        $p = new PageTemplate($report);
        $p->setFormat(Page::FORMAT_A4, Page::ORIENTATION_LANDSCAPE);

        self::assertEquals(Page::DIMENSIONS[Page::FORMAT_A4][1], $p->getWidth());
        self::assertEquals(Page::DIMENSIONS[Page::FORMAT_A4][0], $p->getHeight());
    }
}

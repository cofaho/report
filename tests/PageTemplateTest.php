<?php

use PHPUnit\Framework\TestCase;
use Report\Band\DataBand;
use Report\PageTemplate;

class PageTest extends TestCase
{
    public function testSetMargin()
    {
        $p = new PageTemplate();
        $p->setMargin(2);

        self::assertEquals(2, $p->getMargin(PageTemplate::MARGIN_BOTTOM));
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
        $p = new PageTemplate();
        $p->setFormat(PageTemplate::FORMAT_A4);

        self::assertEquals(PageTemplate::FORMAT_A4[0], $p->getWidth());
        self::assertEquals(PageTemplate::FORMAT_A4[1], $p->getHeight());
    }

    public function testSetOrientation()
    {
        $p = new PageTemplate();
        $p->setFormat(PageTemplate::FORMAT_A4, PageTemplate::ORIENTATION_LANDSCAPE);

        self::assertEquals(PageTemplate::FORMAT_A4[1], $p->getWidth());
        self::assertEquals(PageTemplate::FORMAT_A4[0], $p->getHeight());
    }
}

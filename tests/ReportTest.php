<?php

use PHPUnit\Framework\TestCase;
use Report\Band\DataBand;
use Report\Data\DataSet\ArrayDataSet;
use Report\Element\TextBox;
use Report\PageTemplate;
use Report\Report;

class ReportTest extends TestCase
{
    public function testSerialize()
    {
        $report = new Report(Report::UNITS_PT);
        self::assertEquals('C:13:"Report\Report":29:{a:2:{i:0;s:2:"pt";i:1;a:0:{}}}', serialize($report));
    }

    public function testUnserialize()
    {
        /** @var Report $report */
        $report = unserialize('C:13:"Report\Report":29:{a:2:{i:0;s:2:"pt";i:1;a:0:{}}}');
        self::assertEquals(Report::UNITS_PT, $report->getUserUnits());
    }

    public function testSerializeUnserialize()
    {
        $report = new Report(Report::UNITS_PT);

        $page = new PageTemplate($report);
        $page
            ->setFormat(PageTemplate::FORMAT_A1)
            ->setFreeHeight(100);

        $ds = new ArrayDataSet([['id' => 1]]);

        $band = new DataBand($page);
        $band
            ->setDataSource($ds)
            ->setHeight(20);

        $text = new TextBox($band, 'foo');
        $text->setXY(10, 20);

        $serialized = serialize($report);
        $report = unserialize($serialized);

        self::assertEquals(Report::UNITS_PT, $report->getUserUnits());

        /** @var PageTemplate $page */
        $page = $report->getPages()[0];
        self::assertEquals(PageTemplate::FORMAT_A1[0], $page->getWidth());
        self::assertNull($page->getFreeHeight());

        /** @var DataBand $band */
        $band = $page->getBands()[0];
        self::assertEquals(20, $band->getHeight());
        self::assertEquals($page, $band->getParent());

        $ds = $band->getDataSource();
        $ds->open();
        self::assertEquals(1, $ds->field('id'));

        /** @var TextBox $text */
        $text = $band->getElements()[0];
        self::assertEquals($band, $text->getParent());
        self::assertEquals('foo', $text->getText());
        self::assertEquals(10, $text->getX());
    }
}

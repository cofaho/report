<?php

namespace Report\Element;


use PHPUnit\Framework\TestCase;
use Report\Band\ReportHeader;
use Report\Data\DataSet\ArrayDataSet;
use Report\PageTemplate;

class ImageTest extends TestCase
{
    /**
     * @var Image
     */
    protected $image;

    protected function setUp(): void
    {
        $page = new PageTemplate();
        $band = new ReportHeader($page);
        $this->image = new Image($band);
        $this->image
            ->setSrc(__DIR__ . '/../../_assets/rect50x100.png');
    }

    public function testGetImageBBox()
    {
        $bbox = $this->image->getImageBBox();
        self::assertEquals(50, $bbox->width);
        self::assertEquals(100, $bbox->height);
    }

    public function testFitStretch()
    {
        $this->image
            ->setFit(Image::FIT_STRETCH)
            ->setWidth(10);
        $bbox = $this->image->getImageBBox();
        self::assertEquals(10, $bbox->width);
        self::assertEquals(100, $bbox->height);
    }

    public function testFitKeepRatio()
    {
        $this->image
            ->setFit(Image::FIT_KEEP_RATIO)
            ->setWidth(25);
        $bbox = $this->image->getImageBBox();
        self::assertEquals(25, $bbox->width);
        self::assertEquals(50, $bbox->height);

        $this->image
            ->setWidth(null)
            ->setHeight(200);
        $bbox = $this->image->getImageBBox();
        self::assertEquals(100, $bbox->width);
        self::assertEquals(200, $bbox->height);
    }

    public function testRotatedBBox()
    {
        $this->image->setRotation(90);
        $bbox = $this->image->getBBox();
        self::assertEquals(100, $bbox->width);
        self::assertEquals(50, $bbox->height);
    }

    public function testSetSrc()
    {
        $imgPath = __DIR__ . '/../../_assets/1x1.jpg';
        $ds = new ArrayDataSet([['img' => $imgPath], ['img' => '2.jpg']], 'ds');
        $ds->open();
        $this->image->getParent()->setDataSource($ds);
        $this->image->setSrc('[ds.img]');
        self::assertTrue($this->image->hasExpression());
        self::assertEquals($imgPath, $this->image->getSrc());
    }


}

<?php

namespace Report\Element;


use PHPUnit\Framework\TestCase;
use Report\Band\DataBand;
use Report\Data\DataSet\ArrayDataSet;
use Report\Helper\Color;
use Report\Helper\FontStyle;
use Report\Helper\LineStyle;
use Report\PageTemplate;

class TextBoxTest extends TestCase
{
    /**
     * @var TextBox
     */
    protected $textBox;

    protected function setUp(): void
    {
        $style = new FontStyle([
            'font-size' => 20,
            'line-height' => 24
        ]);
        $ds = new ArrayDataSet([['name' => 'someName']], 'ds');
        $page = new PageTemplate();
        $band = new DataBand($page);
        $band->setDataSource($ds);
        $this->textBox = new TextBox($band);
        $this->textBox
            ->setFontStyle($style)
            ->setText('text text');
        $ds->open();
    }

    public function testSplitNoTopElement()
    {
        $result = $this->textBox->split(1);
        self::assertNull($result[0]);
    }

    public function testSplitNoBottomElement()
    {
        $result = $this->textBox->setHeight(40)->split(30);
        self::assertNull($result[1]);
    }

    public function testSplit()
    {
        $this->textBox->getParent()->setHeight(30);
        list($top, $bottom) = $this->textBox
            ->setWidth(50)
            ->split(30);
        self::assertEquals(30, $top->getRenderHeight());
        self::assertEquals(24, $bottom->getRenderHeight());
    }

    public function testGetBBox()
    {
        $bbox = $this->textBox->getBBox();
        self::assertEqualsWithDelta(72, $bbox->width, 0.1);
        self::assertEquals(24, $bbox->height);
    }

    public function testGetBBoxWithWidthAndNoGrow()
    {
        $bbox = $this->textBox
            ->setWidth(50)
            ->getBBox();
        self::assertEquals(50, $bbox->width);
        self::assertEquals(48, $bbox->height);
    }

    public function testGetBBoxWithBorderAndPadding()
    {
        $line = new LineStyle(new Color(), 1);
        $bbox = $this->textBox->setPadding(1)->getBBox();
        self::assertEqualsWithDelta(74, $bbox->width, 0.1);
        self::assertEquals(26, $bbox->height);
        $this->textBox
            ->setBorderTop($line)
            ->setBorderLeft($line);
        $bbox = $this->textBox->getBBox();
        self::assertEqualsWithDelta(75, $bbox->width, 0.1);
        self::assertEquals(27, $bbox->height);
    }

    public function testGetMaxY()
    {
        self::assertEquals(24, $this->textBox->getMaxY());
        $this->textBox->setY(10);
        self::assertEquals(34, $this->textBox->getMaxY());
    }

    public function testSetWidth()
    {
        $this->textBox->setWidth(40)->setCanGrowHorizontal(false);
        self::assertEquals(40, $this->textBox->getWidth());
        self::assertFalse($this->textBox->canGrowHorizontal());
        $this->textBox->setWidth(null);
        self::assertTrue($this->textBox->canGrowHorizontal());
    }

    public function testGetWidth()
    {
        self::assertNull($this->textBox->getWidth());
        $this->textBox->setWidth(100);
        self::assertEquals(100, $this->textBox->getWidth());
    }

    public function testGetMinWidth()
    {
        self::assertEquals(0, $this->textBox->getMinWidth());
        $this->textBox->setPadding(1);
        self::assertEquals(2, $this->textBox->getMinWidth());
        $line = new LineStyle(new Color(), 1);
        $this->textBox->setBorderLeft($line);
        self::assertEquals(3, $this->textBox->getMinWidth());
    }

    public function testSetHeight()
    {
        $this->textBox->setHeight(40)->setCanGrowVertical(false);
        self::assertEquals(40, $this->textBox->getHeight());
        self::assertFalse($this->textBox->canGrowVertical());
        $this->textBox->setHeight(null);
        self::assertTrue($this->textBox->canGrowVertical());
    }

    public function testGetHeight()
    {
        self::assertNull($this->textBox->getHeight());
        $this->textBox->setHeight(100);
        self::assertEquals(100, $this->textBox->getHeight());
    }

    public function testGetMinHeight()
    {
        self::assertEquals(0, $this->textBox->getMinHeight());
        $this->textBox->setPadding(1);
        self::assertEquals(2, $this->textBox->getMinHeight());
        $line = new LineStyle(new Color(), 1);
        $this->textBox->setBorderTop($line);
        self::assertEquals(3, $this->textBox->getMinHeight());
    }

    public function testSetText()
    {
        self::assertFalse($this->textBox->hasExpression());
        $this->textBox->setText('[someField]');
        self::assertTrue($this->textBox->hasExpression());
    }

    public function testGetText()
    {
        self::assertEquals('text text', $this->textBox->getText());
        $this->textBox->setText('text [ds.name]');
        self::assertEquals('text someName', $this->textBox->getText());
    }

    public function testGetRowText()
    {
        self::assertEquals('text text', $this->textBox->getRawText());
        $this->textBox->setText('text [name]');
        self::assertEquals('text [name]', $this->textBox->getRawText());
    }

    public function testCanGrowHorizontal()
    {
        self::assertTrue($this->textBox->canGrowHorizontal());
        $this->textBox->setWidth(10)->setCanGrowHorizontal(false);
        self::assertFalse($this->textBox->canGrowHorizontal());
        $this->textBox->setCanGrowHorizontal(true);
        self::assertTrue($this->textBox->canGrowHorizontal());
    }

    public function testSetCanGrowHorizontal()
    {
        self::assertTrue($this->textBox->canGrowHorizontal());
        $this->textBox->setWidth(100)->setCanGrowHorizontal(false);
        self::assertFalse($this->textBox->canGrowHorizontal());
        $this->textBox->setWidth(null);
        self::assertTrue($this->textBox->canGrowHorizontal());
    }

    public function testGetSetCanGrowVertical()
    {
        self::assertTrue($this->textBox->canGrowVertical());
        $this->textBox->setHeight(100)->setCanGrowVertical(false);
        self::assertFalse($this->textBox->canGrowVertical());
        $this->textBox->setHeight(null);
        self::assertTrue($this->textBox->canGrowVertical());
    }

    public function testSetStretchToBottom()
    {
        $this->textBox->setHeight(100);
        self::assertFalse($this->textBox->isStretchedToBottom());
        self::assertFalse($this->textBox->canGrowVertical());
        $this->textBox->setStretchToBottom(true);
        self::assertTrue($this->textBox->isStretchedToBottom());
        self::assertTrue($this->textBox->canGrowVertical());
    }

}

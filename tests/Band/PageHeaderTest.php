<?php

namespace Report\Band;


use PHPUnit\Framework\TestCase;
use Report\Element\TextBox;
use Report\PageTemplate;

class PageHeaderTest extends TestCase
{
    /**
     * @var PageHeader
     */
    private $header;

    public function setUp(): void
    {
        $page = new PageTemplate();
        $this->header = new PageHeader($page);
    }

    public function testClone()
    {
        $text = new TextBox($this->header);

        $headerClone = clone $this->header;
        $textClone = $headerClone->getElements()[0];

        self::assertInstanceOf(TextBox::class, $textClone);
        self::assertNotEquals($text, $textClone);
    }

    public function testGetMinHeight()
    {
        $text = new TextBox($this->header);
        $text->setXY(10, 10)->setHeight(40);

        self::assertEquals(50, $this->header->getMinHeight());
    }


}

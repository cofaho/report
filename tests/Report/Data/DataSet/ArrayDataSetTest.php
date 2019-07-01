<?php

namespace Report\Data\DataSet;


use PHPUnit\Framework\TestCase;
use Report\Data\Event\RowChanged;

class ArrayDataSetTest extends TestCase
{
    /**
     * @var ArrayDataSet
     */
    private $arrayDataSet;

    protected function setUp(): void
    {
        $this->arrayDataSet = new ArrayDataSet([
            ['id' => 1, 'name' => 'a'],
            ['id' => 2, 'name' => 'b'],
            ['id' => 3, 'name' => 'c']
        ]);
        $this->arrayDataSet->open();
    }

    protected function tearDown(): void
    {
        $this->arrayDataSet->close();
        $this->arrayDataSet = null;
    }

    // tests
    public function testSetGetName()
    {
        $this->arrayDataSet->setName('ds');
        self::assertEquals('ds', $this->arrayDataSet->getName());
    }

    public function testGetField()
    {
        self::assertEquals(1, $this->arrayDataSet->field('id'));
        self::assertEquals('a', $this->arrayDataSet->name);
    }

    public function testHasRows()
    {
        $emptyDataSet = new ArrayDataSet([]);
        $emptyDataSet->open();
        self::assertTrue($this->arrayDataSet->hasRows());
        self::assertFalse($emptyDataSet->hasRows());
    }

    public function testOpenClose()
    {
        self::assertTrue($this->arrayDataSet->isActive());
        $this->arrayDataSet->close();
        self::assertFalse($this->arrayDataSet->isActive());
        $this->arrayDataSet->open();
        self::assertTrue($this->arrayDataSet->isActive());
    }

    public function testNext()
    {
        $counter = 0;
        $this->arrayDataSet->attachListener(RowChanged::getName(), function() use (&$counter){++$counter;});
        self::assertEquals(0, $this->arrayDataSet->key());
        $this->arrayDataSet->next();
        self::assertEquals(1, $this->arrayDataSet->key());
        $this->arrayDataSet->valid();
        self::assertEquals(1, $counter);
    }

    public function testPrev()
    {
        $this->arrayDataSet->next();
        self::assertEquals(1, $this->arrayDataSet->key());
        self::assertEquals(['id' => 2, 'name' => 'b'], $this->arrayDataSet->current());
        $this->arrayDataSet->prev();
        self::assertEquals(0, $this->arrayDataSet->key());
        self::assertEquals(['id' => 1, 'name' => 'a'], $this->arrayDataSet->current());
    }

    public function testRewind()
    {
        self::assertEquals(0, $this->arrayDataSet->key());
        $this->arrayDataSet->next();
        self::assertNotEquals(0, $this->arrayDataSet->key());
        $this->arrayDataSet->rewind();
        self::assertEquals(0, $this->arrayDataSet->key());
    }

    public function testCurrent()
    {
        self::assertEquals(['id' => 1, 'name' => 'a'], $this->arrayDataSet->current());
        $this->arrayDataSet->next();
        self::assertEquals(['id' => 2, 'name' => 'b'], $this->arrayDataSet->current());
    }

    public function testValid()
    {
        self::assertTrue($this->arrayDataSet->valid());
        $this->arrayDataSet->next();
        $this->arrayDataSet->next();
        self::assertTrue($this->arrayDataSet->valid());
        $this->arrayDataSet->next();
        self::assertFalse($this->arrayDataSet->valid());
    }

    public function testLinkedDataSet()
    {
        $detail = new ArrayDataSet([
            ['parent_id' => 1],
            ['parent_id' => 1],
            ['parent_id' => 2],
            ['parent_id' => 3],
            ['parent_id' => 3]
        ]);
        $detail->setMasterLink('parent_id', $this->arrayDataSet, 'id');

        $this->arrayDataSet->rewind();
        $this->arrayDataSet->valid();
        self::assertEquals(0, $detail->key());
        $this->arrayDataSet->next();
        $this->arrayDataSet->valid();
        self::assertEquals(2, $detail->key());
        $this->arrayDataSet->next();
        $this->arrayDataSet->valid();
        self::assertEquals(3, $detail->key());
    }

}

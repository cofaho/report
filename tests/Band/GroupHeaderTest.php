<?php

namespace Report\Band;

use PHPUnit\Framework\TestCase;
use Report\Data\DataSet\ArrayDataSet;
use Report\PageTemplate;

class GroupHeaderTest extends TestCase
{

    public function testIsGroupFieldChanged()
    {
        $ds = new ArrayDataSet([
            ['group' => 'a'],
            ['group' => 'b'],
            ['group' => 'b'],
            ['group' => 'c']
        ]);

        $page = new PageTemplate();
        $header = new GroupHeader($page);
        $header->setDataSource($ds)->setGroupFieldName('group');

        $ds->open();
        $result = [true, true, false, true];
        while ($ds->valid()) {
            self::assertEquals($result[$ds->key()], $header->isGroupFieldChanged());
            $ds->next();
        }
    }
}

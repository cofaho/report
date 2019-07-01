<?php

namespace Report\Band;

use PHPUnit\Framework\TestCase;
use Report\Data\DataSet\ArrayDataSet;
use Report\PageTemplate;

class GroupFooterTest extends TestCase
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
        $header = new GroupFooter($page);
        $header->setDataSource($ds)->setGroupFieldName('group');

        $ds->open();
        $result = [false, true, false, true, true];
        while ($ds->valid()) {
            self::assertEquals($result[$ds->key()], $header->isGroupFieldChanged());
            $ds->next();
        }
        self::assertEquals($result[$ds->key()], $header->isGroupFieldChanged());
    }
}

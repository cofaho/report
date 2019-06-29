<?php

namespace Report\Data\Event;


use Report\Event\Event;

class RowChanged extends Event
{
    protected static $name = 'onRowChanged';
}

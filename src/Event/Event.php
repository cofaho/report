<?php

namespace Report\Event;


class Event
{
    protected static $name = null;

    public static function getName()
    {
        return self::$name;
    }
}

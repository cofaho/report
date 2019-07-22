<?php

namespace Report\Renderer;


use Report\Report;

abstract class AbstractRenderer
{
    /**
     * @var Scope
     */
    public static $scope = null;
    /**
     * @var string
     */
    protected static $userUnits = Report::UNITS_PX;

    public static function getScope()
    {
        if (self::$scope === null) {
            self::$scope = new Scope();
            self::$scope->totalPages = '<span class="total-pages"></span>';
        }
        return self::$scope;
    }

    /**
     * @return string
     */
    public static function getUserUnits(): string
    {
        return self::$userUnits;
    }
}

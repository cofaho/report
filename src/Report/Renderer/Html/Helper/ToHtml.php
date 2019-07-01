<?php

namespace Report\Renderer\Html\Helper;


use Report\Helper\Color;
use Report\Helper\LineStyle;
use Report\Renderer\Html\ReportRenderer;

class ToHtml
{
    protected static $lineStyle = [
        LineStyle::SOLID => 'solid',
        LineStyle::DASHED => 'dashed',
        LineStyle::DOTTED => 'dotted'
    ];

    public static function Color(Color $color)
    {
        $alfa = $color->getAlfa();
        $alfa = $alfa < 100 ? sprintf('%02X', (round($alfa * 255 / 100))) : '';
        return sprintf('#%06X', $color->getColor()) . $alfa;
    }

    public static function Line(LineStyle $line)
    {
        return sprintf('%s %s%s %s',
            ToHtml::$lineStyle[$line->getStyle()],
            $line->getWidth(), ReportRenderer::getUserUnits(),
            ToHtml::Color($line->getColor())
        );
    }
}

<?php

namespace Report\Renderer\Html\Element;


use Report\Element\Image;
use Report\Renderer\Html\RenderResult;
use Report\Renderer\Html\ReportRenderer;

class ImageRenderer implements ElementRendererInterface
{
    /**
     * @param Image $image
     * @param float $availableHeight
     * @param bool $isFirstBand
     * @return RenderResult
     */
    public static function getRenderResult($image, float $availableHeight, bool $isFirstBand)
    {
        if ($image->getY() > $availableHeight) {
            $tailObject = clone $image;
            $tailObject->setY($image->getY() - $availableHeight);
            return new RenderResult('', $tailObject);
        }

        $tailObject = null;

        $cssClass = 'element image';
        $id = 'image' . spl_object_id($image);
        $cssClass .= ' ' . $id;

        $src = $image->getSrc();

        $content = "<img alt=\"\" src=\"$src\" class=\"$cssClass\">";

        return new RenderResult($content, $tailObject);

    }

    /**
     * @param Image $image
     * @return string
     */
    public static function getStyle($image)
    {
        $units = ReportRenderer::getUserUnits();
        $cssClass = 'image' . spl_object_id($image);
        $css = '';

        $bbox = $image->getImageBBox();

        $x = $image->getX() + $image->getDx();
        $y = $image->getY() + $image->getDy();
        $w = $bbox->width;
        $h = $bbox->height;

        $css .= 'left:' . $x . $units . ';';
        $css .= 'top:' . $y . $units . ';';

        if ($w !== null) {
            $css .= "width:$w$units;";
        }
        if ($h !== null) {
            $css .= "height:$h$units;";
        }

        if ($alfa = $image->getRotation()) {
            $css .= "transform-origin: center; transform: rotate({$alfa}deg);";
        }

        return ".$cssClass { $css }\n";
    }
}

<?php

namespace Report\Renderer\Pdf\Element;


use ReflectionClass;
use ReflectionException;
use Report\Element\ElementInterface;
use Report\Renderer\RenderResult;

class ElementRenderer
{
    /**
     * @param ElementInterface $element
     * @param float $availableHeight
     * @param bool $isFirstBand
     * @return RenderResult
     */
    public static function getRenderResult($element, float $availableHeight, bool $isFirstBand): RenderResult
    {
        try {
            $renderer = 'Report\\Renderer\\Pdf\\Element\\' . (new ReflectionClass($element))->getShortName() . 'Renderer';
            if (method_exists($renderer, 'getRenderResult')) {
                return $renderer::getRenderResult($element, $availableHeight, $isFirstBand);
            }
        } catch (ReflectionException $exception) {

        }
        return new RenderResult();
    }

}

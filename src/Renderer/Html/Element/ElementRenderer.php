<?php

namespace Report\Renderer\Html\Element;


use ReflectionClass;
use ReflectionException;
use Report\Element\ElementInterface;
use Report\Renderer\Html\RenderResult;

class ElementRenderer implements ElementRendererInterface
{
    /**
     * @param ElementInterface $element
     * @param float $availableHeight
     * @param bool $isFirstBand
     * @return RenderResult
     */
    public static function getRenderResult($element, float $availableHeight, bool $isFirstBand)
    {
        try {
            $renderer = 'Report\\Renderer\\Html\\Element\\' . (new ReflectionClass($element))->getShortName() . 'Renderer';
            if (method_exists($renderer, 'getRenderResult')) {
                return $renderer::getRenderResult($element, $availableHeight, $isFirstBand);
            }
        } catch (ReflectionException $exception) {

        }
        return new RenderResult();
    }

    /**
     * @param ElementInterface $element
     * @return string
     */
    public static function getStyle($element)
    {
        try {
            $renderer = 'Report\\Renderer\\Html\\Element\\' . (new ReflectionClass($element))->getShortName() . 'Renderer';
            if (method_exists($renderer, 'getStyle')) {
                return $renderer::getStyle($element);
            }
        } catch (ReflectionException $exception) {

        }
        return '';
    }
}

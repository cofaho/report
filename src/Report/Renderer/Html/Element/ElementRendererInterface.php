<?php

namespace Report\Renderer\Html\Element;


use Report\Element\ElementInterface;
use Report\Renderer\RenderResult;

interface ElementRendererInterface
{
    /**
     * @param ElementInterface $element
     * @param float $availableHeight
     * @param bool $isFirstBand
     * @return RenderResult
     */
    public static function getRenderResult($element, float $availableHeight, bool $isFirstBand): RenderResult;

    /**
     * @param ElementInterface $element
     * @return string
     */
    public static function getStyle($element): string;
}

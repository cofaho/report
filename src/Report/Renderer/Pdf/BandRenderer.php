<?php

namespace Report\Renderer\Pdf;


use FontLib\Exception\FontNotFoundException;
use pdf\Document\Page\PageContents;
use Report\Band\BandInterface;
use Report\Band\Property\PageBreakAfterInterface;
use Report\Band\Property\PageBreakBeforeInterface;
use Report\Element\Container;
use Report\Element\ElementInterface;
use Report\Element\TextBox;
use Report\Property\OnOnePageInterface;
use Report\Renderer\Pdf\Element\ElementRenderer;
use Report\Renderer\RenderResult;

class BandRenderer
{
    /**
     * @param BandInterface $band
     * @param bool $isFirstBand
     * @return RenderResult
     * @throws FontNotFoundException
     */
    public static function getRenderResult(BandInterface $band, $isFirstBand = false): RenderResult
    {
        if ($band instanceof PageBreakBeforeInterface && $band->isPageBreakBefore()) {
            $tailBand = clone $band;
            $tailBand->setPageBreakBefore(false);
            return new RenderResult(null, $tailBand);
        }

        $elements = $band->getElements();
        if (empty($elements)) {
            return new RenderResult();
        }

        $content = new PageContents();
        $tailBand = null;
        $xObjects = [];

        $bandHeight = $band->getMinHeight();

        $freeHeight = $band->getParent()->getFreeHeight();

        foreach ($elements as $element) {
            $element->setRenderWidth(null);
            $element->setRenderHeight(null);
            if ($element instanceof Container) {
                $result = ElementRenderer::getRenderResult($element, $freeHeight, $isFirstBand);
                if ($result->content) {
                    $content->append($result->content);
                }
                if ($result->extra) {
                    $xObjects += $result->extra;
                }

                if (isset($result->tailObject)) {

                    if ($band instanceof OnOnePageInterface && $band->isOnOnePage() && !$isFirstBand) {
                        $tailBand = clone $band;
                        return new RenderResult(null, $tailBand);
                    }

                    if ($tailBand === null) {
                        $tailBand = clone $band;
                        $tailBand
                            ->setHeight(null)
                            ->setMinHeight(null)
                            ->setElements([]);
                    }
                    $tailBand->addElement($result->tailObject);
                }
            }
            $bandHeight = max($bandHeight, $element->getMaxY());
        }
        if ($bandHeight > $freeHeight) {
            $bandHeight = $freeHeight;
        }

        $band->setHeight($bandHeight);

        usort($elements, function(ElementInterface $a, ElementInterface $b) {
            return $b->getMaxY() <=> $a->getMaxY();
        });

        foreach ($elements as $element) {
            if (($element instanceof Container)) {
                continue;
            }

            $isStretchedText = $element instanceof TextBox && $element->isStretchedToBottom();
            $result = ElementRenderer::getRenderResult($element, $freeHeight, $isFirstBand);
            if ($result->content) {
                $content->append($result->content);
            }
            if ($result->extra) {
                $xObjects += $result->extra;
            }

            if (isset($result->tailObject)) {
                if ($band instanceof OnOnePageInterface && $band->isOnOnePage() && !$isFirstBand) {
                    $tailBand = clone $band;
                    return new RenderResult(null, $tailBand);
                }
                if ($tailBand === null) {
                    $tailBand = clone $band;
                    $tailBand
                        ->setHeight(null)
                        ->setMinHeight(null)
                        ->setElements([]);
                }
                $tailBand->addElement($result->tailObject);
            } elseif ($tailBand && $isStretchedText) {
                $texts = $element->split($freeHeight);
                $tailBand->addElement($texts[1]);
            }
        }

        if ($band instanceof PageBreakAfterInterface && $band->isPageBreakAfter() && $tailBand === null) {
            $tailBand = clone $band;
            $tailBand->setElements([]);
            $tailBand->setPageBreakAfter(false);
        }

        return new RenderResult($content, $tailBand, $xObjects);

    }

}

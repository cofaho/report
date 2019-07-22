<?php

namespace Report\Renderer\Html\Element;


use FontLib\Exception\FontNotFoundException;
use Report\Element\TextBox;
use Report\Renderer\Html\Helper\ToHtml;
use Report\Renderer\Html\ReportRenderer;
use Report\Renderer\RenderResult;

class TextBoxRenderer implements ElementRendererInterface
{
    protected static $styleIndex = [];

    /**
     * @param TextBox $text
     * @param float $availableHeight
     * @param bool $isFirstBand
     * @return RenderResult
     * @throws FontNotFoundException
     */
    public static function getRenderResult($text, float $availableHeight, bool $isFirstBand): RenderResult
    {
        if ($text->getY() > $availableHeight) {
            $tailObject = clone $text;
            $tailObject->setY($text->getY() - $availableHeight);
            return new RenderResult('', $tailObject);
        }

        $text->setIsDirty(false);
        if (is_callable($text->onBeforeShow)) {
            $event = ReportRenderer::getScope();
            $event->target = $text;
            ($text->onBeforeShow)($event);
        }
        $hasChanges = $text->isDirty();

        if ($text->getMaxY() > $availableHeight) {
            if ($text->isOnOnePage() && !$isFirstBand) {
                $tailObject = clone $text;
                $tailObject->setIsOnOnePage(false);
                $text = null;
            } else {
                $splitHeight = $availableHeight - $text->getY();
                list($text, $tailObject) = $text->split($splitHeight);
            }

            if ($tailObject) {
                $tailObject->setY(0);
            }
            if ($text === null) {
                return new RenderResult('', $tailObject);
            }

        } else {
            $tailObject = null;
        }

        $cssClass = 'element text';

        $id = 'text' . spl_object_id($text);

        if (isset(self::$styleIndex[$id]) && !$hasChanges) {
            $style = '';
            $cssClass .= ' ' . $id;
        } else {
            $style = self::getCSSProperties($text);
        }

        $units = ReportRenderer::getUserUnits();

        if ($text->isStretchedToBottom()) {
            $h = $text->getRenderHeight();
            if ($text->isHorizontal()) {
                $h -= $text->getVerticalOffsets();
                $style .= "height:$h$units;";
            } else {
                $h -= $text->getHorizontalOffsets();
                $style .= "width:$h$units;";
            }
        }

        $notFixedHeight = $text->hasExpression() || $text->isStretchedToBottom();
        if ($text->getRotation() !== 0 && $notFixedHeight) {
            $x = $text->getX() + $text->getDx();
            $y = $text->getY() + $text->getDy();
            $style .= 'left:' . $x . $units . ';';
            $style .= 'top:' . $y . $units . ';';
        }

        if ($style) {
            $style = " style=\"$style\"";
        }

        $cssClass = " class=\"$cssClass\"";

        $innerHtml = $text->getText();

        if ($text->getTextVerticalAlign() !== TextBox::ALIGN_VERTICAL_TOP) {
            $align = $text->getTextVerticalAlign() === TextBox::ALIGN_VERTICAL_CENTER ? 'middle' : 'bottom';
            $dx = $text->getHorizontalOffsets();
            $dy = $text->getVerticalOffsets();
            if ($text->isHorizontal()) {
                $bbox = $text->getBBox();
                $w = $bbox->width - $dx;
                $h = $bbox->height - $dy;
            } elseif ($text->isVertical()) {
                $bbox = $text->getBBox();
                $w = $bbox->height - $dx;
                $h = $bbox->width - $dy;
            } else {
                $w = $text->getTextBBox()->width - $dx;
                $h = $text->getTextBBox()->height - $dy;
            }
            $innerHtml = "<div class=\"v-align-$align\" style=\"height: $h$units;width:$w$units\">"
                            . $innerHtml
                        . '</div>';
        }

        return new RenderResult(
            "<div$cssClass$style>$innerHtml</div>",
            $tailObject
        );
    }

    /**
     * @param TextBox $text
     * @return string
     */
    public static function getStyle($text): string
    {
        $cssClass = 'text' . spl_object_id($text);
        self::$styleIndex[$cssClass] = 1;
        return ".$cssClass { " . self::getCSSProperties($text) . " }\n";
    }

    /**
     * @param TextBox $text
     * @return string
     */
    protected static function getCSSProperties(TextBox $text): string
    {
        $units = ReportRenderer::getUserUnits();

        $style = '';

        if ($b = $text->getBorderBottom()) {
            $style .= 'border-bottom:' . ToHtml::Line($b) . ';';
        }
        if ($b = $text->getBorderLeft()) {
            $style .= 'border-left:' . ToHtml::Line($b) . ';';
        }
        if ($b = $text->getBorderRight()) {
            $style .= 'border-right:' . ToHtml::Line($b) . ';';
        }
        if ($b = $text->getBorderTop()) {
            $style .= 'border-top:' . ToHtml::Line($b) . ';';
        }

        $fixedHeight = !($text->hasExpression() || $text->isStretchedToBottom());
        if ($text->getRotation() === 0 || $fixedHeight) {
            $x = $text->getX() + $text->getDx();
            $y = $text->getY() + $text->getDy();
            $style .= 'left:' . $x . $units . ';';
            $style .= 'top:' . $y . $units . ';';
        }

        if ($c = $text->getBackgroundColor()) {
            $c = ToHtml::Color($c);
            $style .= "background-color:$c;";
        }

        if ($alfa = $text->getRotation()) {
            $style .= "transform: rotate({$alfa}deg);";
        }

        $aligns = ['left', 'right', 'center', 'justify'];
        $style .= ' text-align:' . $aligns[$text->getTextAlign()] . ';';

        $fontStyle = $text->getFontStyle();
        $style .= 'color:' . ToHtml::Color($fontStyle->getColor()) . ';';
        $style .= 'font-family:' . $fontStyle->getFontFamily() . ';';
        $style .= 'font-size:' . $fontStyle->getFontSize() . $units . ';';
        $style .= 'line-height:' . $fontStyle->getLineHeight() . $units . ';';
        $style .= 'padding:' .
            $text->getPaddingTop() . $units . ' ' .
            $text->getPaddingRight() . $units . ' ' .
            $text->getPaddingBottom() . $units . ' ' .
            $text->getPaddingLeft() . $units . ';';
        $spacing = $fontStyle->getWordSpacing();
        if ($spacing) {
            $style .= sprintf('word-spacing: %s;', $spacing);
        }
        $spacing = $fontStyle->getLetterSpacing();
        if ($spacing) {
            $style .= sprintf('letter-spacing: %s;', $spacing);
        }

        $style .= 'word-break:break-' . ($text->isWordWrap() ? 'word;overflow-wrap:anywhere;' : 'all;');


        $w = $text->getWidth();
        if ($w !== null) {
            $w -= $text->getHorizontalOffsets();
            $style .= "width:$w$units;";
        }

        $h = $text->getHeight();
        if ($h !== null) {
            $h -= $text->getVerticalOffsets();
            $style .= "height:$h$units;";
        }

        return $style;
    }

}

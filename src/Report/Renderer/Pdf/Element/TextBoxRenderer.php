<?php

namespace Report\Renderer\Pdf\Element;


use FontLib\Exception\FontNotFoundException;
use pdf\DataStructure\Matrix;
use pdf\Document\Page\PageContents;
use Report\Element\TextBox;
use Report\Renderer\Helper\Text;
use Report\Renderer\Html\ReportRenderer;
use Report\Renderer\RenderResult;

class TextBoxRenderer
{
    protected static $styleIndex = [];

    /**
     * @param TextBox $textBox
     * @param float $availableHeight
     * @param bool $isFirstBand
     * @return RenderResult
     * @throws FontNotFoundException
     */
    public static function getRenderResult($textBox, float $availableHeight, bool $isFirstBand): RenderResult
    {
        if ($textBox->getY() > $availableHeight) {
            $tailObject = clone $textBox;
            $tailObject->setY($textBox->getY() - $availableHeight);
            return new RenderResult(null, $tailObject);
        }

        $textBox->setIsDirty(false);
        if (is_callable($textBox->onBeforeShow)) {
            $event = ReportRenderer::getScope();
            $event->target = $textBox;
            ($textBox->onBeforeShow)($event);
        }

        if ($textBox->getMaxY() > $availableHeight) {
            if ($textBox->isOnOnePage() && !$isFirstBand) {
                $tailObject = clone $textBox;
                $tailObject->setIsOnOnePage(false);
                $textBox = null;
            } else {
                $splitHeight = $availableHeight - $textBox->getY();
                list($textBox, $tailObject) = $textBox->split($splitHeight);
            }

            if ($tailObject) {
                $tailObject->setY(0);
            }
            if ($textBox === null) {
                return new RenderResult(null, $tailObject);
            }

        } else {
            $tailObject = null;
        }

        $fontStyle = $textBox->getFontStyle();

        $borderTopWidth = $textBox->getBorderTop() === null ? 0 : $textBox->getBorderTop()->getWidth();
        $borderLeftWidth = $textBox->getBorderLeft() === null ? 0 : $textBox->getBorderLeft()->getWidth();
        $borderBottomWidth = $textBox->getBorderBottom() === null ? 0 : $textBox->getBorderBottom()->getWidth();
        $borderRightWidth = $textBox->getBorderRight() === null ? 0 : $textBox->getBorderRight()->getWidth();

        $size = $textBox->getSize();

        $matrix = Matrix::identity();
        if ($textBox->getRotation()) {
            $matrix
                ->translate(-$size->width / 2, $size->height / 2)
                ->rotate(deg2rad($textBox->getRotation()))
                ->translate(
                    $size->width / 2 + $textBox->getDx(),
                    - $size->height / 2 - $textBox->getDy()
                );
        }
        $matrix->translate($textBox->getX(), -$textBox->getY());

        $textMatrix = Matrix::identity();
        $textMatrix->translate(
            $borderLeftWidth + $textBox->getPaddingLeft(),
            -($borderTopWidth + $textBox->getPaddingTop() + 0.75*$fontStyle->getLineHeight())
        );

        $content = new PageContents();
        $content
            ->saveState()
            ->concatCurrentTransformationMatrix($matrix)
            ->setFillRGB($textBox->getBackgroundColor()->toNormalizedArray())
            ->rectangle(0, -$size->height, $size->width, $size->height)
            ->fill()
            ->setFillRGB($fontStyle->getColor()->toNormalizedArray())
            ->beginText()
            ->setMatrix($textMatrix)
            ->setFont('/F1', $fontStyle->getFontSize());

        if ($borderTopWidth) {
            $y = - $borderTopWidth / 2;
            $content
                ->setLineWidth($borderTopWidth)
                ->setStrokeRGB($textBox->getBorderTop()->getColor()->toNormalizedArray())
                ->moveTo(0, $y)
                ->lineTo($size->width, $y)
                ->stroke();
        }
        if ($borderRightWidth) {
            $x = $size->width - $borderRightWidth / 2;
            $content
                ->setLineWidth($borderRightWidth)
                ->setStrokeRGB($textBox->getBorderRight()->getColor()->toNormalizedArray())
                ->moveTo($x, 0)
                ->lineTo($x, -$size->height)
                ->stroke();
        }
        if ($borderBottomWidth) {
            $y = -$size->height + $borderBottomWidth / 2;
            $content
                ->setLineWidth($borderBottomWidth)
                ->setStrokeRGB($textBox->getBorderBottom()->getColor()->toNormalizedArray())
                ->moveTo($size->width, $y)
                ->lineTo(0, $y)
                ->stroke();
        }
        if ($borderLeftWidth) {
            $x = $borderLeftWidth / 2;
            $content
                ->setLineWidth($borderLeftWidth)
                ->setStrokeRGB($textBox->getBorderLeft()->getColor()->toNormalizedArray())
                ->moveTo($x, -$size->height)
                ->lineTo($x, 0)
                ->stroke();
        }

        $rows = $textBox->getWidth() === null
            ? [$textBox->getText()]
            : Text::split($textBox->getText(), $fontStyle, $textBox->getWidth() - $textBox->getHorizontalOffsets());

        if (count($rows) > 1) {
            $content->setLeading($fontStyle->getLineHeight());
        }

        if ($fontStyle->getLetterSpacing()) {
            $content->setCharacterSpacing($fontStyle->getLetterSpacing());
        }

        if ($fontStyle->getWordSpacing()) {
            $content->setWordSpacing($fontStyle->getWordSpacing());
        }

        foreach ($rows as $i => $row) {
            if ($i === 0) {
                $content->addText($row);
            } else {
                $content->addTextOnNextLine($row);
            }
        }

        $content
            ->endText()
            ->restoreState();

        return new RenderResult($content, $tailObject);
    }

}

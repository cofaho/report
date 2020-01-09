<?php

namespace Report\Renderer\Pdf\Element;


use pdf\DataStructure\Matrix;
use pdf\Document\Page\PageContents;
use pdf\Helper\ImageParser;
use Report\Element\Image;
use Report\Renderer\RenderResult;

class ImageRenderer
{
    protected static $images = [];

    /**
     * @param Image $image
     * @param float $availableHeight
     * @param bool $isFirstBand
     * @return RenderResult
     */
    public static function getRenderResult($image, float $availableHeight, bool $isFirstBand): RenderResult
    {
        if ($image->getY() > $availableHeight) {
            $tailObject = clone $image;
            $tailObject->setY($image->getY() - $availableHeight);
            return new RenderResult([], $tailObject);
        }

        $tailObject = null;

        $name = '/img' . md5($image->getSrc());

        if (isset(self::$images[$name])) {
            $pdfImage = null;
            $imgHeader = self::$images[$name];
        } else {
            $parser = new ImageParser($image->getSrc());
            $pdfImage = $parser->getImage();
            $imgHeader = $pdfImage->getHeader();
            $imgHeader->Name = $name;
            self::$images[$name] = $imgHeader;
        }

        $matrix = Matrix::identity();
        $matrix->scale($image->getRenderWidth(), $image->getRenderHeight())->translate(0, -$image->getRenderHeight());

        if ($image->getRotation()) {
            $w2 = $imgHeader->Width / 2;
            $h2 = $imgHeader->Height / 2;
            $matrix
                ->translate(-$w2, $h2)
                ->rotate(deg2rad($image->getRotation()))
                ->translate($w2 + $image->getDx(), -$h2 - $image->getDy());
        }
        $matrix->translate($image->getX(), -$image->getY());

        $content = new PageContents();
        $content
            ->saveState()
            ->concatCurrentTransformationMatrix($matrix)
            ->addXObject($imgHeader->Name)
            ->restoreState();

        return new RenderResult($content, $tailObject, [$pdfImage]);

    }
}

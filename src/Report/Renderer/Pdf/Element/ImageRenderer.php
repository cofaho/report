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
        } else {
            $parser = new ImageParser($image->getSrc());
            $parser->getImage()->getHeader()->Name = $name;
            self::$images[$name] = true;
            $pdfImage = [$parser->getImage()];
        }

        $bbox = $image->getImageBBox();

        $matrix = Matrix::identity();
        $matrix->scale($bbox->width, $bbox->height)->translate(0, -$bbox->height);
        if ($image->getRotation()) {
            $w2 = $bbox->width / 2;
            $h2 = $bbox->height / 2;
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
            ->addXObject($name)
            ->restoreState();

        return new RenderResult($content, $tailObject, $pdfImage);

    }
}

<?php

namespace Report;


use Serializable;

class PageTemplate extends AbstractBandContainer implements Serializable
{
    const ORIENTATION_PORTRAIT = 0;
    const ORIENTATION_LANDSCAPE = 1;

    const MARGIN_TOP = 0;
    const MARGIN_RIGHT = 1;
    const MARGIN_BOTTOM = 2;
    const MARGIN_LEFT = 3;

    // page sizes in mm
    const FORMAT_A0 = [841, 1189];
    const FORMAT_A1 = [594, 841];
    const FORMAT_A2 = [420, 594];
    const FORMAT_A3 = [297, 420];
    const FORMAT_A4 = [210, 297];
    const FORMAT_A5 = [148, 210];
    const FORMAT_A6 = [105, 148];
    const FORMAT_A7 = [74, 105];
    const FORMAT_A8 = [52, 74];
    const FORMAT_A9 = [37, 52];
    const FORMAT_A10 = [26, 37];
    const FORMAT_A11 = [18, 26];
    const FORMAT_A12 = [13, 18];
    const FORMAT_A13 = [9, 13];
    const FORMAT_4A0 = [1682, 2378];
    const FORMAT_2A0 = [1189, 1682];
    const FORMAT_A0_PLUS = [914, 1292];
    const FORMAT_A1_PLUS = [609, 914];
    const FORMAT_A3_PLUS = [329, 483];

    const FORMAT_B0 = [1000, 1414];
    const FORMAT_B1 = [707, 1000];
    const FORMAT_B2 = [500, 707];
    const FORMAT_B3 = [353, 500];
    const FORMAT_B4 = [250, 353];
    const FORMAT_B5 = [176, 250];
    const FORMAT_B6 = [125, 176];
    const FORMAT_B7 = [88, 125];
    const FORMAT_B8 = [62, 88];
    const FORMAT_B9 = [44, 62];
    const FORMAT_B10 = [31, 44];
    const FORMAT_B11 = [22, 31];
    const FORMAT_B12 = [15, 22];
    const FORMAT_B13 = [11, 15];
    const FORMAT_B0_PLUS = [1118, 1580];
    const FORMAT_B1_PLUS = [720, 1020];
    const FORMAT_B2_PLUS = [520, 720];

    const FORMAT_C0 = [917, 1297];
    const FORMAT_C1 = [648, 917];
    const FORMAT_C2 = [458, 648];
    const FORMAT_C3 = [324, 458];
    const FORMAT_C4 = [229, 324];
    const FORMAT_C5 = [162, 229];
    const FORMAT_C6 = [114, 162];
    const FORMAT_C6_7 = [81, 162];
    const FORMAT_C7 = [81, 114];
    const FORMAT_C8 = [57, 81];
    const FORMAT_C9 = [40, 57];
    const FORMAT_C10 = [28, 40];
    const FORMAT_C11 = [20, 28];
    const FORMAT_C12 = [14, 20];
    /**
     * @var float
     */
    protected $width;
    /**
     * @var float
     */
    protected $height;
    /**
     * @var float[]
     */
    protected $margin = [1, 1, 1, 1];
    /**
     * @var float
     */
    protected $freeHeight;


    public function __construct(Report $report = null)
    {
        if ($report) {
            $report->addPage($this);
        }
    }

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize([
            $this->width,
            $this->height,
            $this->margin,
            $this->bands
        ]);
    }

    /**
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        list(
            $this->width,
            $this->height,
            $this->margin,
            $this->bands
        ) = unserialize($serialized);
    }

    /**
     * @param float[] $format
     * @param int $orientation
     * @return $this
     */
    public function setFormat($format, $orientation = self::ORIENTATION_PORTRAIT)
    {
        $this->setWidth($format[$orientation]);
        $this->setHeight($format[1 - $orientation]);
        return $this;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight(float $height): void
    {
        $this->height = $height;
    }

}

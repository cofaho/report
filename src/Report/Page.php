<?php

namespace Report;


class Page
{
    const ORIENTATION_PORTRAIT = 0;
    const ORIENTATION_LANDSCAPE = 1;

    const MARGIN_TOP = 0;
    const MARGIN_RIGHT = 1;
    const MARGIN_BOTTOM = 2;
    const MARGIN_LEFT = 3;

    // page sizes in mm
    const FORMAT_A0 = 'A0';
    const FORMAT_A1 = 'A1';
    const FORMAT_A2 = 'A2';
    const FORMAT_A3 = 'A3';
    const FORMAT_A4 = 'A4';
    const FORMAT_A5 = 'A5';
    const FORMAT_A6 = 'A6';
    const FORMAT_A7 = 'A7';
    const FORMAT_A8 = 'A8';
    const FORMAT_A9 = 'A9';
    const FORMAT_A10 = 'A10';
    const FORMAT_A11 = 'A11';
    const FORMAT_A12 = 'A12';
    const FORMAT_A13 = 'A13';
    const FORMAT_4A0 = '4A0';
    const FORMAT_2A0 = '2A0';
    const FORMAT_A0_PLUS = 'A0_PLUS';
    const FORMAT_A1_PLUS = 'A1_PLUS';
    const FORMAT_A3_PLUS = 'A3_PLUS';

    const FORMAT_B0 = 'B0';
    const FORMAT_B1 = 'B1';
    const FORMAT_B2 = 'B2';
    const FORMAT_B3 = 'B3';
    const FORMAT_B4 = 'B4';
    const FORMAT_B5 = 'B5';
    const FORMAT_B6 = 'B6';
    const FORMAT_B7 = 'B7';
    const FORMAT_B8 = 'B8';
    const FORMAT_B9 = 'B9';
    const FORMAT_B10 = 'B10';
    const FORMAT_B11 = 'B11';
    const FORMAT_B12 = 'B12';
    const FORMAT_B13 = 'B13';
    const FORMAT_B0_PLUS = 'B0_PLUS';
    const FORMAT_B1_PLUS = 'B1_PLUS';
    const FORMAT_B2_PLUS = 'B2_PLUS';

    const FORMAT_C0 = 'C0';
    const FORMAT_C1 = 'C1';
    const FORMAT_C2 = 'C2';
    const FORMAT_C3 = 'C3';
    const FORMAT_C4 = 'C4';
    const FORMAT_C5 = 'C5';
    const FORMAT_C6 = 'C6';
    const FORMAT_C6_7 = 'C6_7';
    const FORMAT_C7 = 'C7';
    const FORMAT_C8 = 'C8';
    const FORMAT_C9 = 'C9';
    const FORMAT_C10 = 'C10';
    const FORMAT_C11 = 'C11';
    const FORMAT_C12 = 'C12';

    const DIMENSIONS = [
        Page::FORMAT_A0 => [841, 1189],
        Page::FORMAT_A1 => [594, 841],
        Page::FORMAT_A2 => [420, 594],
        Page::FORMAT_A3 => [297, 420],
        Page::FORMAT_A4 => [210, 297],
        Page::FORMAT_A5 => [148, 210],
        Page::FORMAT_A6 => [105, 148],
        Page::FORMAT_A7 => [74, 105],
        Page::FORMAT_A8 => [52, 74],
        Page::FORMAT_A9 => [37, 52],
        Page::FORMAT_A10 => [26, 37],
        Page::FORMAT_A11 => [18, 26],
        Page::FORMAT_A12 => [13, 18],
        Page::FORMAT_A13 => [9, 13],
        Page::FORMAT_4A0 => [1682, 2378],
        Page::FORMAT_2A0 => [1189, 1682],
        Page::FORMAT_A0_PLUS => [914, 1292],
        Page::FORMAT_A1_PLUS => [609, 914],
        Page::FORMAT_A3_PLUS => [329, 483],
        Page::FORMAT_B0 => [1000, 1414],
        Page::FORMAT_B1 => [707, 1000],
        Page::FORMAT_B2 => [500, 707],
        Page::FORMAT_B3 => [353, 500],
        Page::FORMAT_B4 => [250, 353],
        Page::FORMAT_B5 => [176, 250],
        Page::FORMAT_B6 => [125, 176],
        Page::FORMAT_B7 => [88, 125],
        Page::FORMAT_B8 => [62, 88],
        Page::FORMAT_B9 => [44, 62],
        Page::FORMAT_B10 => [31, 44],
        Page::FORMAT_B11 => [22, 31],
        Page::FORMAT_B12 => [15, 22],
        Page::FORMAT_B13 => [11, 15],
        Page::FORMAT_B0_PLUS => [1118, 1580],
        Page::FORMAT_B1_PLUS => [720, 1020],
        Page::FORMAT_B2_PLUS => [520, 720],
        Page::FORMAT_C0 => [917, 1297],
        Page::FORMAT_C1 => [648, 917],
        Page::FORMAT_C2 => [458, 648],
        Page::FORMAT_C3 => [324, 458],
        Page::FORMAT_C4 => [229, 324],
        Page::FORMAT_C5 => [162, 229],
        Page::FORMAT_C6 => [114, 162],
        Page::FORMAT_C6_7 => [81, 162],
        Page::FORMAT_C7 => [81, 114],
        Page::FORMAT_C8 => [57, 81],
        Page::FORMAT_C9 => [40, 57],
        Page::FORMAT_C10 => [28, 40],
        Page::FORMAT_C11 => [20, 28],
        Page::FORMAT_C12 => [14, 20],
    ];

    /**
     * Convert format in mm to user units
     * @param array $format
     * @param string $units
     * @param int $dpi
     * @return array
     */
    public static function getFormatInUnits(array $format, string $units, int $dpi = 96)
    {
        $k = [
            Report::UNITS_PX => $dpi / 25.4,
            Report::UNITS_PT => 2.8346472,
            Report::UNITS_MM => 1,
            Report::UNITS_CM => 0.1,
            Report::UNITS_IN => 0.0393701,
        ][$units];

        $format[0] *= $k;
        $format[1] *= $k;

        if ($units == Report::UNITS_PX) {
            $format[0] = round($format[0]);
            $format[1] = round($format[1]);
        }

        return $format;
    }
}

<?php

namespace Report\Renderer\Helper;


use FontLib\Exception\FontNotFoundException;
use Report\Helper\FontStyle;
use Report\Helper\Rectangle;

class Text
{
    /**
     * @param $text
     * @param FontStyle|null $style
     * @param float|null $maxWidth
     * @param bool $wordWrap
     * @param string $encoding
     * @return Rectangle
     * @throws FontNotFoundException
     */
    public static function getTextSize($text, FontStyle $style = null, $maxWidth = null, $wordWrap = true, $encoding = 'UTF-8')
    {
        if ($style === null) {
            $style = new FontStyle();
        }
        $wordsLength = 0;
        $rowWidth = 0;
        $maxRowWidth = 0;
        $fontName = $style->getFontFamily();
        $fontSize = $style->getFontSize();
        $letterSpacing = $style->getLetterSpacing();
        $lineHeight = $style->getLineHeight();
        $textHeight = $lineHeight;
        $textLength = mb_strlen($text, $encoding);
        $char = null;
        $charCode = null;
        $prevCode = null;
        $spaceCode = mb_ord(' ', $encoding);
        $spaceWidth = self::getCharWidth($fontName, $spaceCode) * $fontSize + $letterSpacing + $style->getWordSpacing();
        for($i = 0; $i < $textLength; ++$i) {
            $prevCode = $charCode;
            $char = mb_substr($text, $i, 1, $encoding);
            $charCode = mb_ord($char, $encoding);
            if ($charCode === $spaceCode) {
                $charWidth = $spaceWidth;
                if ($wordWrap) {
                    $wordsLength = $rowWidth;
                }
            } else {
                $charWidth = self::getCharWidth($fontName, $charCode, $prevCode) * $fontSize + $letterSpacing;
            }
            $rowWidth += $charWidth;
            if ($maxWidth !== null && $rowWidth > $maxWidth) {
                if ($wordWrap && $wordsLength) {
                    $maxRowWidth = max($maxRowWidth, $wordsLength);
                    $rowWidth -= $wordsLength + $spaceWidth;
                    $prevCode = null;
                    $wordsLength = 0;
                } else {
                    $maxRowWidth = max($maxRowWidth, $rowWidth - $charWidth);
                    $rowWidth = $charWidth;
                }
                $textHeight += $lineHeight;
            } elseif ($char === "\n") {
                $maxRowWidth = max($maxRowWidth, $wordsLength);
                $rowWidth = 0;
                $prevCode = null;
                $wordsLength = 0;
                $textHeight += $lineHeight;
            }
        }
        $maxRowWidth = max($maxRowWidth, $rowWidth);

        return new Rectangle([
            'width' => $maxRowWidth,
            'height' => $textHeight
        ]);
    }

    /**
     * @param $text
     * @param FontStyle|null $style
     * @param null $maxWidth
     * @param bool $wordWrap
     * @param string $encoding
     * @return string[]
     * @throws FontNotFoundException
     */
    public static function split($text, FontStyle $style = null, $maxWidth = null, $wordWrap = true, $encoding = 'UTF-8')
    {
        if ($style === null) {
            $style = new FontStyle();
        }
        $wordsLength = 0;
        $rowWidth = 0;
        $fontName = $style->getFontFamily();
        $fontSize = $style->getFontSize();
        $letterSpacing = $style->getLetterSpacing();
        $textLength = mb_strlen($text, $encoding);
        $char = null;
        $charCode = null;
        $prevCode = null;
        $spaceCode = mb_ord(' ', $encoding);
        $spaceWidth = self::getCharWidth($fontName, $spaceCode) * $fontSize + $letterSpacing + $style->getWordSpacing();
        $rows = [];
        $start = 0;
        $end = 0;
        for($i = 0; $i < $textLength; ++$i) {
            $prevCode = $charCode;
            $char = mb_substr($text, $i, 1, $encoding);
            $charCode = mb_ord($char, $encoding);
            if ($charCode === $spaceCode) {
                $charWidth = $spaceWidth;
                if ($wordWrap) {
                    $wordsLength = $rowWidth;
                    $end = $i;
                }
            } else {
                $charWidth = self::getCharWidth($fontName, $charCode, $prevCode) * $fontSize + $letterSpacing;
            }
            $rowWidth += $charWidth;
            if ($maxWidth !== null && $rowWidth > $maxWidth) {
                $canWrapWord = $wordWrap && $wordsLength;
                if ($canWrapWord) {
                    $rowWidth -= $wordsLength + $spaceWidth;
                    $prevCode = null;
                    $wordsLength = 0;
                } else {
                    $rowWidth = $charWidth;
                    $end = $i;
                }
                $rows[] = mb_substr($text, $start, $end - $start, $encoding);
                $start = $end;
                if ($canWrapWord) {
                    // skip first space
                    ++$start;
                }
            } elseif ($char === "\n") {
                $end = $i;
                $rows[] = mb_substr($text, $start, $end - $start, $encoding);
                $rowWidth = 0;
                $prevCode = null;
                $start = $end + 1;
            }

        }
        $rows[] = mb_substr($text, $start);

        return $rows;
    }

    /**
     * @param string $text
     * @param FontStyle $style
     * @param float $width
     * @param float $height
     * @param bool $wordWrap
     * @param string $encoding
     * @return int
     * @throws FontNotFoundException
     */
    public static function strlenInRect($text, FontStyle $style, $width, $height, $wordWrap = true, $encoding = 'UTF-8')
    {
        $wordsLength = 0;
        $rowWidth = 0;
        $fontName = $style->getFontFamily();
        $fontSize = $style->getFontSize();
        $letterSpacing = $style->getLetterSpacing();
        $lineHeight = $style->getLineHeight();
        $textHeight = $lineHeight;
        $textLength = mb_strlen($text, $encoding);
        $char = null;
        $charCode = null;
        $prevCode = null;
        $spaceCode = mb_ord(' ', $encoding);
        $spaceWidth = self::getCharWidth($fontName, $spaceCode) * $fontSize + $letterSpacing + $style->getWordSpacing();
        $length = 0;
        for($i = 0; $i < $textLength && $textHeight <= $height; ++$i) {
            $prevCode = $charCode;
            $char = mb_substr($text, $i, 1, $encoding);
            $charCode = mb_ord($char, $encoding);
            if ($charCode === $spaceCode) {
                $charWidth = $spaceWidth;
                if ($wordWrap) {
                    $wordsLength = $rowWidth;
                    $length = $i;
                }
            } else {
                $charWidth = self::getCharWidth($fontName, $charCode, $prevCode) * $fontSize + $letterSpacing;
            }
            $rowWidth += $charWidth;
            if ($rowWidth > $width) {
                if ($wordWrap) {
                    $rowWidth -= $wordsLength + $spaceWidth;
                    $prevCode = null;
                    $wordsLength = 0;
                } else {
                    $rowWidth = $charWidth;
                    $length = $i;
                }
                $textHeight += $lineHeight;
            } elseif ($char === "\n") {
                $length = $i;
                $rowWidth = 0;
                $prevCode = null;
                $wordsLength = 0;
                $textHeight += $lineHeight;
            }
        }

        if ($i === $textLength && $textHeight <= $height) {
            return $textLength;
        }

        return $length;
    }

    /**
     * @param $fontName
     * @param int $charCode
     * @param null|int $prevCharCode
     * @return float|null width in em
     * @throws FontNotFoundException
     */
    protected static function getCharWidth($fontName, $charCode, $prevCharCode = null)
    {
        $fontData = FontData::load($fontName);

        if (!$fontData) {
            return null;
        }

        $rightGlyphId = $fontData->charMap[$charCode] ?? 0;
        $width = $fontData->widths[$rightGlyphId];
        if ($prevCharCode !== null) {
            $leftGlyphId = $fontData->charMap[$prevCharCode] ?? 0;
            if ($fontData->kernTree
                && isset($fontData->kernTree[$leftGlyphId])
                && isset($fontData->kernTree[$leftGlyphId][$rightGlyphId])) {
                $width -= $fontData->kernTree[$leftGlyphId][$rightGlyphId];
            }
        }

        return $width / $fontData->unitsPerEm;
    }
}

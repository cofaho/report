<?php

namespace Report\Helper;


class FontStyle
{
    /**
     * @var string
     */
    protected $fontFamily = 'Roboto';
    /**
     * @var float
     */
    protected $fontSize = 14;
    /**
     * @var float
     */
    protected $lineHeight = 16.8;
    /**
     * @var float
     */
    protected $letterSpacing = 0;
    /**
     * @var float
     */
    protected $wordSpacing = 0;
    /**
     * @var Color
     */
    protected $color;

    public function __construct($style = null)
    {
        if ($style === null) {
            return;
        }
        $this->setFontFamily($style['font-family'] ?? 'Roboto');
        $this->setFontSize($style['font-size'] ?? 14);
        $this->setLineHeight($style['line-height'] ?? $this->getFontSize() * 1.2);
        $this->setLetterSpacing($style['letter-spacing'] ?? 0);
        $this->setWordSpacing($style['word-spacing'] ?? 0);
        $this->setColor($style['color'] ?? new Color());
    }

    /**
     * @return string
     */
    public function getFontFamily(): string
    {
        return $this->fontFamily;
    }

    /**
     * @param string $fontFamily
     * @return $this
     */
    public function setFontFamily(string $fontFamily): FontStyle
    {
        $this->fontFamily = $fontFamily;
        return $this;
    }

    /**
     * @return float
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * @param float $fontSize
     * @return $this
     */
    public function setFontSize($fontSize): FontStyle
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * @return float
     */
    public function getLineHeight()
    {
        return $this->lineHeight;
    }

    /**
     * @param float $lineHeight
     * @return $this
     */
    public function setLineHeight($lineHeight): FontStyle
    {
        $this->lineHeight = $lineHeight;
        return $this;
    }

    /**
     * @return float
     */
    public function getLetterSpacing()
    {
        return $this->letterSpacing;
    }

    /**
     * @param float $letterSpacing
     * @return $this
     */
    public function setLetterSpacing($letterSpacing): FontStyle
    {
        $this->letterSpacing = $letterSpacing;
        return $this;
    }

    /**
     * @return float
     */
    public function getWordSpacing()
    {
        return $this->wordSpacing;
    }

    /**
     * @param float $wordSpacing
     * @return $this
     */
    public function setWordSpacing($wordSpacing): FontStyle
    {
        $this->wordSpacing = $wordSpacing;
        return $this;
    }

    /**
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param Color $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }
}

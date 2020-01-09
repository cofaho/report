<?php

namespace Report\Element;


use Closure;
use FontLib\Exception\FontNotFoundException;
use Report\Renderer\AbstractRenderer;
use Report\Band\BandInterface;
use Report\Data\Event\RowChanged;
use Report\Helper\Color;
use Report\Helper\FontStyle;
use Report\Helper\LineStyle;
use Report\Helper\Rectangle;
use Report\Renderer\Helper\Text;

class TextBox extends AbstractElement
{
    const ALIGN_LEFT = 0;

    const ALIGN_RIGHT = 1;

    const ALIGN_CENTER = 2;

    const ALIGN_JUSTIFY = 3;

    const ALIGN_VERTICAL_TOP = 0;

    const ALIGN_VERTICAL_CENTER = 1;

    const ALIGN_VERTICAL_BOTTOM = 2;

    /**
     * @var Closure|null
     */
    public $onBeforeShow = null;
    /**
     * @var float|null
     */
    protected $width = null;
    /**
     * @var float|null
     */
    protected $renderWidth = null;
    /**
     * @var float|null
     */
    protected $height = null;
    /**
     * @var float|null
     */
    protected $renderHeight = null;
    /**
     * @var string|null
     */
    protected $text = null;
    /**
     * @var bool
     */
    protected $wordWrap = true;
    /**
     * @var int
     */
    protected $textAlign = self::ALIGN_LEFT;
    /**
     * @var int
     */
    protected $textVerticalAlign = self::ALIGN_VERTICAL_TOP;
    /**
     * @var FontStyle
     */
    protected $fontStyle = null;
    /**
     * @var Color|null
     */
    protected $backgroundColor = null;
    /**
     * @var LineStyle|null
     */
    protected $borderTop = null;
    /**
     * @var LineStyle|null
     */
    protected $borderRight = null;
    /**
     * @var LineStyle|null
     */
    protected $borderBottom = null;
    /**
     * @var LineStyle|null
     */
    protected $borderLeft = null;
    /**
     * @var float
     */
    protected $paddingTop = 0;
    /**
     * @var float
     */
    protected $paddingRight = 0;
    /**
     * @var float
     */
    protected $paddingBottom = 0;
    /**
     * @var float
     */
    protected $paddingLeft = 0;
    /**
     * @var bool
     */
    protected $growHorizontal = true;
    /**
     * @var bool
     */
    protected $growVertical = true;
    /**
     * @var bool
     */
    protected $stretchToBottom = false;
    /**
     * @var bool
     */
    protected $hasExpression = false;
    /**
     * @var string
     */
    protected static $expressionRegexp = '/\[([\w\.]+)\]/';
    /**
     * @var bool
     */
    protected $isDirty = false;

    /**
     * Text constructor.
     * @param string $text
     * @param BandInterface $band
     */
    public function __construct(BandInterface $band, string $text = '')
    {
        parent::__construct($band);

        $this->setText($text);
    }

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize([
            $this->isOnOnePage,
            $this->name,
            $this->x,
            $this->y,
            $this->width,
            $this->height,
            $this->rotation,
            $this->band,
            $this->text,
            $this->textAlign,
            $this->textVerticalAlign,
            $this->fontStyle,
            $this->backgroundColor,
            $this->borderTop,
            $this->borderRight,
            $this->borderBottom,
            $this->borderLeft,
            $this->paddingTop,
            $this->paddingRight,
            $this->paddingBottom,
            $this->paddingLeft,
            $this->growHorizontal,
            $this->growVertical,
            $this->stretchToBottom,
            $this->hasExpression
        ]);
    }

    /**
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        list(
            $this->isOnOnePage,
            $this->name,
            $this->x,
            $this->y,
            $this->width,
            $this->height,
            $this->rotation,
            $this->band,
            $this->text,
            $this->textAlign,
            $this->textVerticalAlign,
            $this->fontStyle,
            $this->backgroundColor,
            $this->borderTop,
            $this->borderRight,
            $this->borderBottom,
            $this->borderLeft,
            $this->paddingTop,
            $this->paddingRight,
            $this->paddingBottom,
            $this->paddingLeft,
            $this->growHorizontal,
            $this->growVertical,
            $this->stretchToBottom,
            $this->hasExpression
        ) = unserialize($serialized);

        $this->initEvents();
    }

    /**
     * @param float $splitHeight
     * @return TextBox[]
     * @throws FontNotFoundException
     */
    public function split(float $splitHeight): array
    {
        $dx = $this->getHorizontalOffsets();
        $borderTop = $this->getBorderTop();
        $dy = $this->getPaddingTop() + ($borderTop ? $borderTop->getWidth() : 0);
        $textBBox = $this->getTextSize();

        $textWidth = $textBBox->width - $dx;
        $textHeight = $textBBox->height - $dy;

        if ($this->isHorizontal()) {
            $textHeight = min($textHeight, $splitHeight - $dy);
        } else {
            $textWidth = min($textWidth, $splitHeight - $dx);
        }

        $length = Text::strlenInRect(
            $this->getText(),
            $this->getFontStyle(),
            $textWidth,
            $textHeight,
            $this->isWordWrap()
        );

        $top = clone $this;
        $bottom = clone $this;

        $top
            ->setText(mb_substr($this->getText(), 0, $length, 'UTF-8'))
            ->setHeight($splitHeight);

        $bottom->setText(ltrim(mb_substr($this->getText(), $length, null, 'UTF-8')));

        if ($length === 0 && !$this->isStretchedToBottom()) {
            return [null, $bottom];
        }

        if ($length === mb_strlen($this->getText(), 'UTF-8') && !$this->isStretchedToBottom()) {
            return [$top, null];
        }

        $top->setStretchToBottom(true);

        if ($this->isVertical()) {
            $r = $this->getRotation();
            if ($r === 90 || $r === -270) {
                $top->setBorderRight(null);
                $bottom->setBorderLeft(null);
            } else {
                $top->setBorderLeft(null);
                $bottom->setBorderRight(null);
            }
            $top->setHeight($this->getRenderWidth());
            $bottom
                ->setHeight($this->getRenderWidth())
                ->setWidth(null);
        } elseif ($this->isHorizontal()) {
            $top
                ->setWidth($this->getRenderWidth())
                ->setBorderBottom(null);

            $bottom
                ->setWidth($this->getRenderWidth())
                ->setBorderTop(null);
        }

        return [$top, $bottom];
    }

    /**
     * @return Rectangle
     * @throws FontNotFoundException
     */
    public function getTextSize()
    {
        if ($this->bbox !== null && isset($this->bbox[0])) {
            return clone $this->bbox[0];
        }

        $dx = $this->getHorizontalOffsets();
        $minWidth = $this->getMinWidth();
        $textWidth = $this->canGrowHorizontal() ? null : $minWidth - $dx;

        $size = Text::getTextSize(
            $this->getText(),
            $this->getFontStyle(),
            $textWidth,
            $this->isWordWrap()
        );

        $size->x = $this->x;
        $size->y = $this->y;

        $size->width += $dx;
        $size->width = max($size->width, $minWidth);

        $size->height += $this->getVerticalOffsets();
        $size->height = max($size->height, $this->getMinHeight());

        $this->bbox[0] = $size;

        return clone $size;
    }

    /**
     * @return Rectangle
     * @throws FontNotFoundException
     */
    public function getSize()
    {
        $size = $this->getTextSize();

        if ($this->isStretchedToBottom()) {
            if ($this->isHorizontal()) {
                $size->height = $this->getParent()->getHeight() - $this->getY();
            } elseif ($this->isVertical()) {
                $size->width = $this->getParent()->getHeight() - $this->getY();
            }
        }

        return $size;
    }

    /**
     * @return Rectangle
     * @throws FontNotFoundException
     */
    public function getBBox(): Rectangle
    {
        if ($this->bbox !== null && isset($this->bbox[1])) {
            return clone $this->bbox[1];
        }

        $bbox = $this->getSize();

        if ($alfa = $this->getRotation()) {
            $alfa = deg2rad($alfa);
            $sin = abs(sin($alfa));
            $cos = abs(cos($alfa));
            $w = $bbox->width * $cos + $bbox->height * $sin;
            $h = $bbox->width * $sin + $bbox->height * $cos;
            $this->dx = ($w - $bbox->width) / 2;
            $this->dy = ($h - $bbox->height) / 2;
            $bbox->width = $w;
            $bbox->height = $h;
        } else {
            $this->dx = 0;
            $this->dy = 0;
        }

        $bbox->width = max($bbox->width, $this->renderWidth);
        $bbox->height = max($bbox->height, $this->renderHeight);

        $this->bbox[1] = $bbox;

        return clone $bbox;
    }

    /**
     * @return float
     * @throws FontNotFoundException
     */
    public function getMaxY(): float
    {
        $wasStretched = $this->isStretchedToBottom();
        if ($wasStretched) {
            $this->setStretchToBottom(false);
        }

        $y = $this->getY() + $this->getRenderHeight();

        if ($wasStretched) {
            $this->setStretchToBottom(true);
        }

        return $y;
    }

    /**
     * @param float|null $width
     * @return $this
     */
    public function setWidth(?float $width)
    {
        $this->width = $width;
        $this->setCanGrowHorizontal($width === null);
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getWidth(): ?float
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getMinWidth(): float
    {
        return (float)max($this->getWidth(), $this->getHorizontalOffsets());
    }


    /**
     * @return float
     * @throws FontNotFoundException
     */
    public function getRenderWidth(): float
    {
        return $this->renderWidth ?: $this->getBBox()->width;
    }

    /**
     * @param float $width
     * @return TextBox
     */
    public function setRenderWidth(?float $width): TextBox
    {
        $this->renderWidth = $width;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @param float|null $height
     * @return $this
     */
    public function setHeight(?float $height)
    {
        $this->height = $height;
        $this->setCanGrowVertical($height === null);
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getHeight(): ?float
    {
        return $this->height;
    }

    /**
     * @return float
     */
    public function getMinHeight(): float
    {
        return (float)max($this->getHeight(), $this->getVerticalOffsets());
    }

    /**
     * @return float
     * @throws FontNotFoundException
     */
    public function getRenderHeight(): float
    {
        return $this->renderHeight ?: $this->getBBox()->height;
    }

    /**
     * @param float $height
     * @return TextBox
     */
    public function setRenderHeight(?float $height): TextBox
    {
        $this->renderHeight = $height;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @param null|string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        $this->bbox = null;
        $this->isDirty = true;
        $this->initEvents();
        return $this;
    }

    /**
     * @return null|string
     */
    public function getText(): ?string
    {
        $text = $this->text;

        if ($this->hasExpression) {
            $scope = AbstractRenderer::getScope();
            $text = preg_replace_callback(self::$expressionRegexp, function(array $matches) use ($scope) {
                return $scope->getExpressionResult($matches[0]);
            }, $text);
        }

        return $text;
    }

    /**
     * @return null|string
     */
    public function getRawText(): ?string
    {
        return $this->text;
    }

    /**
     * @return bool
     */
    public function isWordWrap(): bool
    {
        return $this->wordWrap;
    }

    /**
     * @param bool $wordWrap
     * @return $this
     */
    public function setWordWrap(bool $wordWrap)
    {
        $this->wordWrap = $wordWrap;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return Color|null
     */
    public function getBackgroundColor(): ?Color
    {
        return $this->backgroundColor;
    }

    /**
     * @param Color|null $backgroundColor
     * @return $this
     */
    public function setBackgroundColor(?Color $backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @param LineStyle|null $border
     * @return TextBox
     */
    public function setBorders(?LineStyle $border)
    {
        $this->setBorderTop($border)
            ->setBorderRight($border)
            ->setBorderLeft($border)
            ->setBorderBottom($border);

        return $this;
    }

    /**
     * @return LineStyle|null
     */
    public function getBorderTop(): ?LineStyle
    {
        return $this->borderTop;
    }

    /**
     * @param LineStyle|null $borderTop
     * @return $this
     */
    public function setBorderTop(?LineStyle $borderTop)
    {
        $this->borderTop = $borderTop;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return LineStyle|null
     */
    public function getBorderRight(): ?LineStyle
    {
        return $this->borderRight;
    }

    /**
     * @param LineStyle|null $borderRight
     * @return $this
     */
    public function setBorderRight($borderRight)
    {
        $this->borderRight = $borderRight;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return LineStyle|null
     */
    public function getBorderBottom(): ?LineStyle
    {
        return $this->borderBottom;
    }

    /**
     * @param LineStyle|null $borderBottom
     * @return $this
     */
    public function setBorderBottom(?LineStyle $borderBottom)
    {
        $this->borderBottom = $borderBottom;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return LineStyle|null
     */
    public function getBorderLeft(): ?LineStyle
    {
        return $this->borderLeft;
    }

    /**
     * @param LineStyle|null $borderLeft
     * @return $this
     */
    public function setBorderLeft(?LineStyle $borderLeft)
    {
        $this->borderLeft = $borderLeft;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return int
     */
    public function getTextAlign(): int
    {
        return $this->textAlign;
    }

    /**
     * @param int $textAlign
     * @return $this
     */
    public function setTextAlign(int $textAlign)
    {
        $this->textAlign = $textAlign;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return FontStyle|null
     */
    public function getFontStyle(): ?FontStyle
    {
        return $this->fontStyle;
    }

    /**
     * @param FontStyle $fontStyle
     * @return $this
     */
    public function setFontStyle(?FontStyle $fontStyle)
    {
        $this->fontStyle = $fontStyle;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function canGrowHorizontal(): bool
    {
        return $this->growHorizontal || $this->width === null;
    }

    /**
     * @param bool $growHorizontal
     * @return $this
     */
    public function setCanGrowHorizontal(bool $growHorizontal)
    {
        $this->growHorizontal = $growHorizontal;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function canGrowVertical(): bool
    {
        return $this->growVertical || $this->getHeight() === null;
    }

    /**
     * @param bool $growVertical
     * @return $this
     */
    public function setCanGrowVertical(bool $growVertical)
    {
        $this->growVertical = $growVertical;
        if ($growVertical) {
            $this->bbox = null;
            $this->isDirty = true;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getTextVerticalAlign(): int
    {
        return $this->textVerticalAlign;
    }

    /**
     * @param int $textVerticalAlign
     * @return $this
     */
    public function setTextVerticalAlign(int $textVerticalAlign)
    {
        $this->textVerticalAlign = $textVerticalAlign;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStretchedToBottom(): bool
    {
        return $this->stretchToBottom;
    }

    /**
     * @param bool $stretchToBottom
     * @return $this
     */
    public function setStretchToBottom(bool $stretchToBottom)
    {
        if ($this->isHorizontalOrVertical() || !$stretchToBottom) {
            $this->stretchToBottom = $stretchToBottom;
            if ($stretchToBottom) {
                $this->setCanGrowVertical(true);
            }
            $this->bbox = null;
            $this->isDirty = true;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function hasExpression(): bool
    {
        return $this->hasExpression;
    }

    /**
     * @return float
     */
    public function getPaddingTop(): float
    {
        return $this->paddingTop;
    }

    /**
     * @param float $paddingTop
     * @return TextBox
     */
    public function setPaddingTop(float $paddingTop)
    {
        $this->paddingTop = $paddingTop;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return float
     */
    public function getPaddingRight(): float
    {
        return $this->paddingRight;
    }

    /**
     * @param float $paddingRight
     * @return TextBox
     */
    public function setPaddingRight(float $paddingRight)
    {
        $this->paddingRight = $paddingRight;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return float
     */
    public function getPaddingBottom(): float
    {
        return $this->paddingBottom;
    }

    /**
     * @param float $paddingBottom
     * @return TextBox
     */
    public function setPaddingBottom(float $paddingBottom)
    {
        $this->paddingBottom = $paddingBottom;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @return float
     */
    public function getPaddingLeft(): float
    {
        return $this->paddingLeft;
    }

    /**
     * @param float $paddingLeft
     * @return TextBox
     */
    public function setPaddingLeft(float $paddingLeft)
    {
        $this->paddingLeft = $paddingLeft;
        $this->bbox = null;
        $this->isDirty = true;
        return $this;
    }

    /**
     * @param float $padding
     * @return TextBox
     */
    public function setPadding(float $padding)
    {
        $this->setPaddingTop($padding);
        $this->setPaddingRight($padding);
        $this->setPaddingBottom($padding);
        $this->setPaddingLeft($padding);
        return $this;
    }

    /**
     * @return float
     */
    public function getHorizontalOffsets(): float
    {
        $result = $this->getPaddingRight() + $this->getPaddingLeft();
        if ($b = $this->getBorderLeft()) {
            $result += $b->getWidth();
        }
        if ($b = $this->getBorderRight()) {
            $result += $b->getWidth();
        }

        return $result;
    }

    /**
     * @return float
     */
    public function getVerticalOffsets(): float
    {
        $result = $this->getPaddingTop() + $this->getPaddingBottom();
        if ($b = $this->getBorderTop()) {
            $result += $b->getWidth();
        }
        if ($b = $this->getBorderBottom()) {
            $result += $b->getWidth();
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->isDirty;
    }

    /**
     * @param bool $isDirty
     * @return $this
     */
    public function setIsDirty(bool $isDirty)
    {
        $this->isDirty = $isDirty;
        return $this;
    }

    /**
     * @param float $rotation
     * @return $this
     */
    public function setRotation(float $rotation)
    {
        parent::setRotation($rotation);
        if (!$this->isHorizontalOrVertical()) {
            $this->setStretchToBottom(false);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isHorizontalOrVertical()
    {
        return in_array($this->getRotation(), [0, 90, -90, 180, -180, 270, -270]);
    }

    /**
     * @return bool
     */
    public function isHorizontal()
    {
        return in_array($this->getRotation(), [0, 180, -180]);
    }

    /**
     * @return bool
     */
    public function isVertical()
    {
        return in_array($this->getRotation(), [90, -90, 270, -270]);
    }

    protected function initEvents()
    {
        $ds = $this->band->getDataSource();
        $hadExpression = $this->hasExpression;
        $this->hasExpression = preg_match(self::$expressionRegexp, $this->getRawText());

        if ($ds) {
            if ($hadExpression && !$this->hasExpression) {
                $ds->detachListener(RowChanged::getName(), [$this, 'onDatasetRowChanged']);
            } elseif ($this->hasExpression && !$hadExpression || $this->isStretchedToBottom()) {
                $ds->attachListener(RowChanged::getName(), [$this, 'onDatasetRowChanged']);
            }
        }
    }

    public function onDatasetRowChanged()
    {
        $this->bbox = null;
    }

}

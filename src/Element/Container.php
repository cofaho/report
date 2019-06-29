<?php

namespace Report\Element;


use Exception;
use Report\AbstractBandContainer;
use Report\Renderer\AbstractRenderer;
use Report\Band\BandInterface;
use Report\Helper\Rectangle;
use Report\PageTemplate;
use Report\Property\Name;
use Report\Property\OnOnePage;
use Serializable;

class Container extends AbstractBandContainer implements ElementInterface, Serializable
{
    use OnOnePage, Name;
    /**
     * @var float
     */
    protected $x;
    /**
     * @var float
     */
    protected $y;
    /**
     * @var float
     */
    protected $height = 0;
    /**
     * @var BandInterface
     */
    protected $band;
    /**
     * @var float
     */
    protected $renderWidth = 0;
    /**
     * @var float
     */
    protected $renderHeight = 0;
    /**
     * @var null|string
     */
    protected $name = null;

    /**
     * Container constructor.
     * @param BandInterface $band
     */
    public function __construct(BandInterface $band)
    {
        $this->band = $band;
        $band->addElement($this);
    }

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize([
            $this->isOnOnePage,
            $this->bands,
            $this->width,
            $this->margin,
            $this->x,
            $this->y,
            $this->band
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
            $this->bands,
            $this->width,
            $this->margin,
            $this->x,
            $this->y,
            $this->band
        ) = unserialize($serialized);
    }

    /**
     * @param float $x
     * @return $this
     */
    public function setX(float $x)
    {
        $this->x = $x;
        return $this;
    }

    /**
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * @param float $y
     * @return $this
     */
    public function setY(float $y)
    {
        $this->y = $y;
        return $this;
    }

    /**
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }

    public function getMaxY(): float
    {
        return $this->getY() + $this->getRenderHeight();
    }


    /**
     * @param float $x
     * @param float $y
     * @return $this
     */
    public function setXY(float $x, float $y)
    {
        $this->setX($x);
        $this->setY($y);
        return $this;
    }

    /**
     * @return Rectangle
     */
    public function getBBox(): Rectangle
    {
        return new Rectangle([
            'x' => $this->getX(),
            'y' => $this->getY(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight()
        ]);
    }

    /**
     * @return float
     */
    public function getRotation(): float
    {
        return 0;
    }

    /**
     * @param float $rotation
     * @throws Exception
     */
    public function setRotation(float $rotation)
    {
        throw new Exception('Unable rotate container');
    }

    /**
     * @param BandInterface $band
     * @return Container
     */
    public function setParent(BandInterface $band)
    {
        $this->band = $band;
        return $this;
    }

    /**
     * @return BandInterface
     */
    public function getParent(): BandInterface
    {
        return $this->band;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return (float)$this->height + $this->getMargin(PageTemplate::MARGIN_TOP) + $this->getMargin(PageTemplate::MARGIN_BOTTOM);
    }

    /**
     * @param float $height
     * @return $this
     */
    public function setHeight(?float $height)
    {
        $this->height = $height;
        return $this;
    }
    /**
     * @param float $height
     * @return $this
     */
    public function addHeight(float $height)
    {
        $this->height += $height;
        return $this;
    }

    /**
     * @return float
     */
    public function getMinHeight(): ?float
    {
        return 0;
    }

    /**
     * @return float
     */
    public function getMinWidth(): float
    {
        return 0;
    }

    /**
     * @param float $width
     * @return $this
     */
    public function setRenderWidth(?float $width)
    {
        $this->renderWidth = $width;
        return $this;
    }

    /**
     * @return float
     */
    public function getRenderWidth(): float
    {
        return $this->getWidth();
    }

    /**
     * @return float
     */
    public function getRenderHeight(): float
    {
        $this->renderHeight = max($this->renderHeight, $this->getHeight());
        return (float)$this->renderHeight;
    }

    /**
     * @param float $height
     * @return $this
     */
    public function setRenderHeight(?float $height)
    {
        $this->renderHeight = $height;
        return $this;
    }

    /**
     * @return float
     */
    public function getDx(): float
    {
        return 0;
    }

    /**
     * @return float
     */
    public function getDy(): float
    {
        return 0;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return $this
     * @throws Exception
     */
    public function setName(?string $name)
    {
        $scope = AbstractRenderer::getScope();

        if ($this->name !== null) {
            $scope->deleteVariable($this->name);
        }

        $this->name = $name;

        if ($this->name !== null) {
            $scope->registerVariable($this->name, $this);
        }

        return $this;
    }
}

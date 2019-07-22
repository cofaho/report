<?php

namespace Report\Renderer\Html\Element;


use FontLib\Exception\FontNotFoundException;
use Generator;
use Report\Band\BandExtension;
use Report\Band\BandInterface;
use Report\Band\DataBand;
use Report\Band\DataFooter;
use Report\Band\DataHeader;
use Report\Band\GroupFooter;
use Report\Band\GroupHeader;
use Report\Element\Container;
use Report\Page;
use Report\Renderer\Html\BandRenderer;
use Report\Renderer\Html\ReportRenderer;
use Report\Renderer\RenderResult;

class ContainerRenderer implements ElementRendererInterface
{
    /**
     * @var array
     */
    protected $cache = [];
    /**
     * @var BandInterface[]
     */
    protected $headers = [];
    /**
     * @var BandInterface[]
     */
    protected $footers = [];
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var bool first non header band
     */
    protected $isFirstBand = true;
    /**
     * @var string
     */
    protected $content = '';
    /**
     * @var array
     */
    protected static $containerGenerator = [];
    /**
     * @var array
     */
    protected static $containerY = [];

    /**
     * @param Container $container
     * @param float $availableHeight
     * @param bool $isFirstBand
     * @return RenderResult
     * @throws FontNotFoundException
     */
    public static function getRenderResult($container, float $availableHeight, bool $isFirstBand): RenderResult
    {
        $id = spl_object_id($container);

        $baseY = null;
        $nextY = null;
        $containerBand = null;

        if (isset(self::$containerGenerator[$id])) {
            list($containerGenerator, $baseY, $nextY, $containerBand, $doNext) = self::$containerGenerator[$id];
            $container->setY($nextY);
            if ($containerGenerator === null) {
                $renderer = new ContainerRenderer();
                $containerGenerator = $renderer->getContainerGenerator($container, $availableHeight);
            }
            if ($doNext) {
                $containerGenerator->next();
            }
        } else {
            if (isset(self::$containerY[$id])) {
                $container->setY(self::$containerY[$id]);
                unset(self::$containerY[$id]);
            }
            $renderer = new ContainerRenderer();
            $containerGenerator = $renderer->getContainerGenerator($container, $availableHeight);
        }

        $content = $containerGenerator->current();

        $tailElement = null;

        if ($containerGenerator->key()) {

            if ($container->isOnOnePage() && !$isFirstBand) {
                $content = '';
                $containerGenerator = null;
                $doNext = false;
            } else {
                $doNext = true;
            }

            $tailElement = $container;
            if ($baseY === null) {
                $baseY = $container->getY();
                $containerBand = $container->getParent();
            }
            if ($nextY !== 0) {
                $nextY = $tailElement->getY() > $availableHeight ? $tailElement->getY() - $availableHeight : 0;
            }
            $id = spl_object_id($tailElement);
            self::$containerGenerator[$id] = [$containerGenerator, $baseY, $nextY, $containerBand, $doNext];
        } elseif (isset(self::$containerGenerator[$id])) {
            $container->setParent($containerBand);
            self::$containerY[$id] = $baseY;
            unset(self::$containerGenerator[$id]);
        }

        return new RenderResult($content, $tailElement);
    }

    /**
     * @param Container $container
     * @param float $availableHeight
     * @return Generator
     * @throws FontNotFoundException
     */
    public function getContainerGenerator(Container $container, float $availableHeight): Generator
    {
        $this->container = $container;

        if ($container->getY() > $availableHeight) {
            yield true => '';
        }

        yield from $this->startContainer();

        foreach($container->getBands(DataBand::class) as $band) {
            yield from $this->renderDataBands($band);
        }

        yield from $this->endContainer();

        yield false => $this->content;
    }

    /**
     * @param Container $container
     * @return string
     */
    public static function getStyle($container): string
    {
        $style = '';
        foreach($container->getBands() as $band) {
            $style .= BandRenderer::getStyle($band);
            foreach ($band->getBands() as $childBand) {
                $style .= BandRenderer::getStyle($childBand);
            }
        }

        return $style;
    }

    /**
     * @return Generator
     * @throws FontNotFoundException
     */
    protected function startContainer(): Generator
    {
        $this->content = '';
        $this->isFirstBand = true;

        $margins = $this->container->getMargin(Page::MARGIN_TOP) + $this->container->getMargin(Page::MARGIN_BOTTOM);
        $parentContainer = $this->container->getParent()->getParent();

        $this->container
            ->setHeight(0)
            ->setFreeHeight($parentContainer->getFreeHeight() - $this->container->getY() - $margins);

        foreach ($this->footers as $footer) {
            $this->container->subFreeHeight($footer->getHeight());
        }

        foreach ($this->headers as $header) {
            yield from $this->getBandGenerator($header);
        }
    }

    /**
     * @return Generator
     * @throws FontNotFoundException
     */
    protected function endContainer(): Generator
    {
        foreach ($this->footers as $footer) {
            yield from $this->getBandGenerator($footer);
        }
        $units = ReportRenderer::getUserUnits();
        $marginTop = $this->container->getMargin(Page::MARGIN_TOP);
        $marginBottom = $this->container->getMargin(Page::MARGIN_BOTTOM);
        $marginLeft = $this->container->getMargin(Page::MARGIN_LEFT);
        $marginRight = $this->container->getMargin(Page::MARGIN_RIGHT);
        $width = $this->container->getWidth() - $marginLeft - $marginRight;
        $height = $this->container->getHeight();
        $style =
            'left:' . $this->container->getX() . $units .
            ';top:' . $this->container->getY() . $units .
            ';width:' . $width . $units .
            ';height:' . $height . $units .
            ';padding:' . $marginTop . $units . ' ' . $marginRight . $units . ' ' .
                $marginBottom . $units . ' ' . $marginLeft . $units . ';';

        $this->container->setRenderHeight($height);

        if (!empty($this->content)) {
            $this->content = '<div class="element container" style="' . $style . '">' . $this->content . '</div>';
        }
    }

    /**
     * @param BandInterface $band
     * @return Generator
     * @throws FontNotFoundException
     */
    protected function getBandGenerator(BandInterface $band): Generator
    {
        if ($this->container->getFreeHeight() == 0) {
            yield from $this->endContainer();
            yield true => $this->content;
            yield from $this->startContainer();
        }

        $result = BandRenderer::getRenderResult($band, $this->isFirstBand);
        if (!empty($result->content)) {
            $this->content .= $result->content;
            $h = $band->getHeight();
            $this->container->addHeight($h);
            $this->container->subFreeHeight($h);
        }

        while ($result->tailObject) {
            $band = $result->tailObject;
            yield from $this->endContainer();
            yield true => $this->content;
            yield from $this->startContainer();
            $result = BandRenderer::getRenderResult($band, $this->isFirstBand);
            if (!empty($result->content)) {
                $this->content .= $result->content;
                $h = $band->getHeight();
                $this->container->addHeight($h);
                $this->container->subFreeHeight($h);
            }
        }
    }

    /**
     * @param BandInterface $mainBand
     * @return Generator
     * @throws FontNotFoundException
     */
    protected function renderDataBands(BandInterface $mainBand): Generator
    {
        $id = spl_object_id($mainBand);
        /** @var DataHeader $dataHeader */
        $dataHeader = null;
        /** @var DataFooter $dataFooter */
        $dataFooter = null;
        /** @var GroupHeader[] $groupHeaders */
        $groupHeaders = [];
        /** @var GroupFooter[] $groupFooters */
        $groupFooters = [];
        $extraBands = [];

        if (isset($this->cache[$id])) {
            list(
                'header' => $dataHeader,
                'footer' => $dataFooter,
                'groupHeaders' => $groupHeaders,
                'groupFooters' => $groupFooters,
                'extra' => $extraBands,
                ) = $this->cache[$id];
        } else {
            $bands = $mainBand->getBands();
            foreach ($bands as $band) {
                switch (true) {
                    case $band instanceof DataHeader:
                        $dataHeader = $band;
                        break;
                    case $band instanceof DataFooter:
                        $dataFooter = $band;
                        break;
                    case $band instanceof DataBand:
                    case $band instanceof BandExtension:
                        $extraBands[] = $band;
                        break;
                    case $band instanceof GroupHeader:
                        $groupHeaders[] = $band;
                        break;
                    case $band instanceof GroupFooter:
                        $groupFooters[] = $band;
                        break;
                }
            }

            if (!isset($mainBand->isTail)) {
                $this->cache[$id] = [
                    'header' => $dataHeader,
                    'footer' => $dataFooter,
                    'groupHeaders' => $groupHeaders,
                    'groupFooters' => $groupFooters,
                    'extra' => $extraBands
                ];
            }
        }

        $ds = $mainBand->getDataSource();
        $ds->isActive() ? $ds->rewind() : $ds->open();
        $hasData = $ds && $ds->hasRows();

        if ($mainBand instanceof DataBand) {

            if ($hasData || $mainBand->isPrintedOnEmptyDataSet()) {
                if ($dataHeader) {
                    yield from $this->getBandGenerator($dataHeader);
                }

                $pushHeader = $dataHeader && $dataHeader->isPrintedOnEveryPage();
                $pushFooter = $dataFooter && $dataFooter->isPrintedOnEveryPage();
                if ($pushHeader) {
                    $this->headers[] = $dataHeader;
                }

                foreach ($groupHeaders as $groupHeader) {
                    if ($groupHeader->isPrintedOnEveryPage()) {
                        $this->headers[] = $groupHeader;
                    }
                }

                foreach ($groupFooters as $groupFooter) {
                    if ($groupFooter->isPrintedOnEveryPage()) {
                        $this->footers[] = $groupFooter;
                    }
                }
                if ($pushFooter) {
                    $this->footers[] = $dataFooter;
                }

                $isFirstRender = true;
                while ($ds->valid() || $mainBand->isPrintedOnEmptyDataSet()) {
                    if ($ds->valid() || $isFirstRender) {
                        if (count($groupHeaders)) {
                            foreach ($groupHeaders as $groupHeader) {
                                if ($groupHeader->isGroupFieldChanged()) {
                                    yield from $this->getBandGenerator($groupHeader);
                                }
                            }
                        }
                        yield from $this->getBandGenerator($mainBand);
                        $isFirstRender = false;
                        $this->isFirstBand = false;
                    }

                    foreach ($extraBands as $extraBand) {
                        yield from $this->renderDataBands($extraBand);
                    }

                    if (!$ds->valid()) {
                        break;
                    }

                    $ds->next();

                    if (count($groupFooters)) {
                        foreach ($groupFooters as $groupFooter) {
                            if ($groupFooter->isGroupFieldChanged()) {
                                $ds->prev();
                                if ($ds->valid()) {
                                    yield from $this->getBandGenerator($groupFooter);
                                }
                                $ds->next();
                            }
                        }
                    }
                }

                foreach ($groupHeaders as $groupHeader) {
                    if ($groupHeader->isPrintedOnEveryPage()) {
                        array_pop($this->headers);
                    }
                    $groupHeader->setGroupValue(null);
                }

                foreach ($groupFooters as $groupFooter) {
                    if ($groupFooter->isPrintedOnEveryPage()) {
                        array_pop($this->footers);
                    }
                    $groupFooter->setGroupValue(null);
                }

                if ($pushHeader) {
                    array_pop($this->headers);
                }

                if ($pushFooter) {
                    array_pop($this->footers);
                }

                if ($dataFooter) {
                    yield from $this->getBandGenerator($dataFooter);
                }
            }
        } else {
            yield from $this->getBandGenerator($mainBand);

            foreach ($extraBands as $extraBand) {
                yield from $this->getBandGenerator($extraBand);
            }
        }
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param Container $container
     * @return $this
     */
    public function setContainer(Container $container): ContainerRenderer
    {
        $this->container = $container;
        return $this;
    }

}

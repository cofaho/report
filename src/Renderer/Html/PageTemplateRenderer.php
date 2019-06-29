<?php

namespace Report\Renderer\Html;


use FontLib\Exception\FontNotFoundException;
use Report\Band\BandExtension;
use Report\Band\BandInterface;
use Report\Band\DataBand;
use Report\Band\DataFooter;
use Report\Band\DataHeader;
use Report\Band\GroupFooter;
use Report\Band\GroupHeader;
use Report\Band\PageFooter;
use Report\Band\PageHeader;
use Report\Band\ReportFooter;
use Report\Band\ReportHeader;
use Report\PageTemplate;
use Report\Renderer\Html\Writer\WriterInterface;

class PageTemplateRenderer
{
    /**
     * @var array
     */
    protected static $cache = [];
    /**
     * @var BandInterface[]
     */
    protected static $headers = [];
    /**
     * @var BandInterface[]
     */
    protected static $footers = [];
    /**
     * @var PageTemplate
     */
    protected static $page;
    /**
     * @var WriterInterface
     */
    protected static $writer;
    /**
     * @var bool first non header band
     */
    protected static $isFirstBand = true;
    /**
     * @var float
     */
    protected static $footerHeight = 0;

    /**
     * @param PageTemplate $page
     * @param WriterInterface $writer
     * @throws FontNotFoundException
     */
    public static function render(PageTemplate $page, WriterInterface $writer)
    {
        self::$page = $page;
        self::$writer = $writer;

        ReportRenderer::getScope()->pageNumber = 0;

        self::startPage();

        if ($band = $page->getBand(ReportHeader::class)) {
            self::renderBand($band);
        }

        foreach($page->getBands(DataBand::class) as $band) {
            self::renderDataBands($band);
        }

        if ($band = $page->getBand(ReportFooter::class)) {
            self::renderBand($band);
        }

        self::endPage();
    }

    /**
     * @param PageTemplate $page
     * @return string
     */
    public static function getStyle(PageTemplate $page)
    {
        $style = '';
        foreach($page->getBands() as $band) {
            $style .= BandRenderer::getStyle($band);
            foreach ($band->getBands() as $childBand) {
                $style .= BandRenderer::getStyle($childBand);
            }
        }

        return $style;
    }

    /**
     * @throws FontNotFoundException
     */
    protected static function startPage()
    {
        self::$isFirstBand = true;

        ++ReportRenderer::getScope()->pageNumber;

        $units = ReportRenderer::getUserUnits();

        $marginTop = self::$page->getMargin(PageTemplate::MARGIN_TOP);
        $marginBottom = self::$page->getMargin(PageTemplate::MARGIN_BOTTOM);
        $marginLeft = self::$page->getMargin(PageTemplate::MARGIN_LEFT);
        $marginRight = self::$page->getMargin(PageTemplate::MARGIN_RIGHT);
        $height = self::$page->getHeight() - $marginTop - $marginBottom;
        $style = 'style="width:' . self::$page->getWidth() . $units .
            ';height:' . $height . $units .
            ';padding:' . $marginTop . $units . ' ' .
                $marginRight . $units . ' ' .
                $marginBottom . $units . ' ' .
                $marginLeft . $units . '"';
        self::$writer->write('<div class="page" ' . $style . '>');

        self::$page->setFreeHeight($height);

        self::$footerHeight = 0;
        $footer = self::$page->getBand(PageFooter::class);
        if ($footer) {
            $h = $footer->getHeight();
            self::$footerHeight += $h;
            self::$page->subFreeHeight($h);
        }
        foreach (self::$footers as $footer) {
            $h = $footer->getHeight();
            self::$footerHeight += $h;
            self::$page->subFreeHeight($h);
        }

        if ($bands = self::$page->getBand(PageHeader::class)) {
            self::renderBand($bands);
        }

        foreach (self::$headers as $header) {
            self::renderBand($header);
        }

    }

    /**
     * @throws FontNotFoundException
     */
    protected static function endPage()
    {
        $dh = self::$page->getFreeHeight();
        self::$page->setFreeHeight(self::$footerHeight);
        foreach (self::$footers as $footer) {
            self::renderBand($footer);
        }

        $footer = self::$page->getBand(PageFooter::class);
        if ($footer) {
            $units = ReportRenderer::getUserUnits();
            if ($dh > 0.1) {
                self::$writer->write("<div class=\"band\" style=\"height:$dh$units\"></div>");
            }
            self::renderBand($footer);
        }
        self::$writer->write('</div>');
    }

    /**
     * @param BandInterface $band
     * @throws FontNotFoundException
     */
    protected static function renderBand(BandInterface $band)
    {
        if (self::$page->getFreeHeight() == 0) {
            self::endPage();
            self::startPage();
        }

        $result = BandRenderer::getRenderResult($band, self::$isFirstBand);
        if (!empty($result->content)) {
            self::$writer->write($result->content);
            $h = $band->getHeight();
            self::$page->subFreeHeight($h);
        }

        while ($result->tailObject) {
            self::endPage();
            $band = $result->tailObject;
            self::startPage();
            $result = BandRenderer::getRenderResult($band, self::$isFirstBand);
            if (!empty($result->content)) {
                self::$writer->write($result->content);
                $h = $band->getHeight();
                self::$page->subFreeHeight($h);
            }
        }
    }

    /**
     * @param BandInterface $mainBand
     * @throws FontNotFoundException
     */
    protected static function renderDataBands(BandInterface $mainBand)
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
        if (isset(self::$cache[$id])) {
            list(
                'header' => $dataHeader,
                'footer' => $dataFooter,
                'groupHeaders' => $groupHeaders,
                'groupFooters' => $groupFooters,
                'extra' => $extraBands,
                ) = self::$cache[$id];
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

            self::$cache[$id] = [
                'header' => $dataHeader,
                'footer' => $dataFooter,
                'groupHeaders' => $groupHeaders,
                'groupFooters' => $groupFooters,
                'extra' => $extraBands
            ];
        }

        $ds = $mainBand->getDataSource();
        $ds->isActive() ? $ds->rewind() : $ds->open();
        $hasData = $ds && $ds->hasRows();

        if ($mainBand instanceof DataBand) {

            if ($hasData || $mainBand->isPrintedOnEmptyDataSet()) {
                if ($dataHeader) {
                    self::renderBand($dataHeader);
                }

                $pushHeader = $dataHeader && $dataHeader->isPrintedOnEveryPage();
                $pushFooter = $dataFooter && $dataFooter->isPrintedOnEveryPage();
                if ($pushHeader) {
                    self::$headers[] = $dataHeader;
                }

                foreach ($groupHeaders as $groupHeader) {
                    if ($groupHeader->isPrintedOnEveryPage()) {
                        self::$headers[] = $groupHeader;
                    }
                }

                foreach ($groupFooters as $groupFooter) {
                    if ($groupFooter->isPrintedOnEveryPage()) {
                        self::$footers[] = $groupFooter;
                    }
                }

                if ($pushFooter) {
                    self::$footers[] = $dataFooter;
                }

                $isFirstRender = true;
                while ($ds->valid() || $mainBand->isPrintedOnEmptyDataSet()) {
                    if ($ds->valid() || $isFirstRender) {
                        if (count($groupHeaders)) {
                            foreach ($groupHeaders as $groupHeader) {
                                if ($groupHeader->isGroupFieldChanged()) {
                                    self::renderBand($groupHeader);
                                }
                            }
                        }

                        self::renderBand($mainBand);
                        $isFirstRender = false;
                        self::$isFirstBand = false;
                    }


                    foreach ($extraBands as $extraBand) {
                        self::renderDataBands($extraBand);
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
                                    self::renderBand($groupFooter);
                                }
                                $ds->next();
                            }
                        }
                    }
                }

                foreach ($groupHeaders as $groupHeader) {
                    if ($groupHeader->isPrintedOnEveryPage()) {
                        array_pop(self::$headers);
                    }
                }

                foreach ($groupFooters as $groupFooter) {
                    if ($groupFooter->isPrintedOnEveryPage()) {
                        array_pop(self::$footers);
                    }
                }

                if ($pushHeader) {
                    array_pop(self::$headers);
                }

                if ($pushFooter) {
                    array_pop(self::$footers);
                }

                if ($dataFooter) {
                    self::renderBand($dataFooter);
                }
            }

        } else {
            self::renderBand($mainBand);

            foreach ($extraBands as $extraBand) {
                self::renderBand($extraBand);
            }
        }
    }

}

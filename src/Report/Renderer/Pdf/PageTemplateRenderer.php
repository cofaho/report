<?php

namespace Report\Renderer\Pdf;


use Exception;
use FontLib\Exception\FontNotFoundException;
use pdf\DataStructure\Matrix;
use pdf\Document\Page\ContentStream;
use pdf\ObjectType\IndirectObject;
use pdf\PDF;
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
use Report\Page;
use Report\PageTemplate;
use Report\Report;

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
     * @var PDF
     */
    protected static $pdf;
    /**
     * @var ContentStream
     */
    protected static $pageContent;
    /**
     * @var bool first non header band
     */
    protected static $isFirstBand = true;
    /**
     * @var float
     */
    protected static $footerHeight = 0;
    /**
     * @var IndirectObject
     */
    protected static $ioPages;

    protected static $unitSizes = [
        Report::UNITS_PX => 0,
        Report::UNITS_PT => 1,
        Report::UNITS_IN => 72,
        Report::UNITS_CM => 28.3465,
        Report::UNITS_MM => 2.83465
    ];

    protected static $unitSize;

    /**
     * @param PageTemplate $page
     * @param PDF $pdf
     * @throws FontNotFoundException
     */
    public static function render(PageTemplate $page, $pdf)
    {
        self::$page = $page;
        self::$pdf = $pdf;

        self::$unitSizes[Report::UNITS_PX] = 72 / $page->getDpi();
        self::$unitSize = self::$unitSizes[ReportRenderer::getUserUnits()];

        self::$ioPages = $pdf->addPages();

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

    protected static function translate($x, $y)
    {
        $matrix = Matrix::identity();
        $matrix->translate($x, $y);
        self::$pageContent->getPageDescription()->concatCurrentTransformationMatrix($matrix);
    }

    /**
     * @throws FontNotFoundException
     * @throws Exception
     */
    protected static function startPage()
    {
        self::$isFirstBand = true;

        ++ReportRenderer::getScope()->pageNumber;

        $marginTop = self::$page->getMargin(Page::MARGIN_TOP);
        $marginBottom = self::$page->getMargin(Page::MARGIN_BOTTOM);
        $height = self::$page->getHeight() - $marginTop - $marginBottom;
        self::$page->setFreeHeight($height);

        self::$footerHeight = 0;
        $footer = self::$page->getBand(PageFooter::class);
        if ($footer) {
            self::$footerHeight += $footer->getHeight();
        }
        foreach (self::$footers as $footer) {
            self::$footerHeight += $footer->getHeight();
        }
        self::$page->subFreeHeight(self::$footerHeight);

        $unitSize = self::$unitSizes[ReportRenderer::getUserUnits()];

        self::$pdf->addPage(self::$ioPages, [self::$page->getWidth(), self::$page->getHeight()], $unitSize);

        self::$pageContent = new ContentStream();
        self::translate(
            self::$page->getMargin(Page::MARGIN_LEFT),
            self::$page->getHeight() - self::$page->getMargin(Page::MARGIN_TOP)
        );

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
        self::$page->setFreeHeight(self::$footerHeight);
        foreach (self::$footers as $footer) {
            self::renderBand($footer);
        }

        $footer = self::$page->getBand(PageFooter::class);
        if ($footer) {
            self::renderBand($footer);
        }

        if (self::$pageContent) {
            self::$pdf->writePageObject(self::$pageContent);
            self::$pageContent = null;
        }
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
        if ($result->content !== null) {
            self::$pageContent->data->append($result->content);
            $h = $band->getHeight();
            self::$page->subFreeHeight($h);
            self::translate(0, -$h);
        }

        while ($result->tailObject) {
            self::endPage();
            $band = $result->tailObject;
            self::startPage();
            $result = BandRenderer::getRenderResult($band, self::$isFirstBand);
            if ($result->content !== null) {
                self::$pageContent->data->append($result->content);
                $h = $band->getHeight();
                self::$page->subFreeHeight($h);
                self::translate(0, -$h);
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
                    $groupFooter->initGroupValue();
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

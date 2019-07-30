<?php

namespace Report\Renderer\Pdf;


use Exception;
use FontLib\Exception\FontNotFoundException;
use pdf\PDF;
use pdf\Writer\WriterInterface;
use Report\Renderer\AbstractRenderer;
use Report\Report;

class ReportRenderer extends AbstractRenderer
{

    /**
     * @param Report $report
     * @param WriterInterface $writer
     * @throws FontNotFoundException
     * @throws Exception
     */
    public static function render(Report $report, WriterInterface $writer)
    {
        self::$userUnits = $report->getUserUnits();

        $pdf = new PDF($writer);

        self::$scope->pageNumber = 0;

        foreach ($report->getPages() as $page) {
            PageTemplateRenderer::render($page, $pdf);
        }

        $pdf->save();
    }

}

<?php

namespace Report\Renderer\Html;


use FontLib\Exception\FontNotFoundException;
use Report\Renderer\AbstractRenderer;
use Report\Renderer\Html\Writer\WriterInterface;
use Report\Report;

class ReportRenderer extends AbstractRenderer
{

    /**
     * @param Report $report
     * @param WriterInterface $writer
     * @throws FontNotFoundException
     */
    public static function render(Report $report, WriterInterface $writer)
    {
        self::$userUnits = $report->getUserUnits();

        self::getScope()->pageNumber = 0;

        foreach ($report->getPages() as $page) {
            $writer->write('<div class="report">');

            PageTemplateRenderer::render($page, $writer);

            $writer->write('</div>');
        }

        $pages = self::getScope()->pageNumber;

        $writer->write('<style>.total-pages:before {content: "' . $pages . '";}</style>');
    }

    /**
     * @param Report $report
     * @return string
     */
    public static function getStyle(Report $report)
    {
        self::$userUnits = $report->getUserUnits();
        $style = '';
        foreach ($report->getPages() as $page) {
            $style .= PageTemplateRenderer::getStyle($page);
        }
        return $style;
    }

}

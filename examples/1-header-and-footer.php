<?php

use Report\Band\PageFooter;
use Report\Band\PageHeader;
use Report\Helper\Color;
use Report\Helper\FontStyle;
use Report\Helper\LineStyle;
use Report\Element\TextBox;
use Report\PageTemplate;
use Report\Renderer\Html\ReportRenderer;
use Report\Renderer\Html\Writer\EchoWriter;
use Report\Report;

require_once 'vendor/autoload.php';

$report = new Report(Report::UNITS_PX);
$page = new PageTemplate($report);
$page->setFormat([700, 800]);
$page->setMargin(10);

//-- Styles --
$color = new Color('#F0F0FF');
$style = new FontStyle([
    'font-size' => 20,
    'line-height' => 24
]);
$solidLine = new LineStyle(new Color('#000'), 1);

//-- Page header --
$pageHeader = new PageHeader($page);
$text = new TextBox($pageHeader, 'Page header');
$text
    ->setBackgroundColor($color)
    ->setXY(20, 0)
    ->setWidth(650)
    ->setFontStyle($style)
    ->setPadding(5)
    ->setBorderBottom($solidLine)
    ->setTextAlign(TextBox::ALIGN_CENTER)
    ->setTextVerticalAlign(TextBox::ALIGN_VERTICAL_CENTER);

//-- Page footer --
$pageFooter = new PageFooter($page);
$text = new TextBox($pageFooter, 'Page [pageNumber] of [totalPages]');
$text
    ->setBackgroundColor($color)
    ->setTextAlign(TextBox::ALIGN_RIGHT)
    ->setXY(20, 0)
    ->setWidth(650)
    ->setBorderTop($solidLine)
    ->setFontStyle($style)
    ->setPadding(5);

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link type="text/css" rel="stylesheet" href="vendor/cofaho/report/src/Renderer/Html/assets/report.css">
    <style>
        <?=ReportRenderer::getStyle($report)?>
    </style>
</head>
<body>
    <?php ReportRenderer::render($report, new EchoWriter()); ?>
</body>
</html>


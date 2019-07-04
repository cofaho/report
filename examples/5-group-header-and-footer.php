<?php

use Report\Band\DataBand;
use Report\Band\GroupFooter;use Report\Band\GroupHeader;use Report\Band\PageFooter;
use Report\Band\PageHeader;
use Report\Data\DataSet\ArrayDataSet;
use Report\Helper\Color;
use Report\Helper\FontStyle;
use Report\Helper\LineStyle;
use Report\Element\TextBox;
use Report\Page;
use Report\PageTemplate;
use Report\Renderer\Html\ReportRenderer;
use Report\Renderer\Html\Writer\EchoWriter;
use Report\Report;

require_once 'vendor/autoload.php';

$report = new Report(Report::UNITS_PX);
$page = new PageTemplate($report);
$page->setFormat(Page::FORMAT_A4);
$page->setMargin(10);

//-- Styles --
$color_blue = new Color('#F0F0FF');
$color_green = new Color('#F0FFF0');
$color_gray = new Color('#CCC');
$style = new FontStyle([
    'font-size' => 20,
    'line-height' => 24
]);
$solidLine = new LineStyle(new Color('#000'), 1);

//-- Page header --
$pageHeader = new PageHeader($page);
$text = new TextBox($pageHeader, 'Page header');
$text
    ->setBackgroundColor($color_blue)
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
    ->setBackgroundColor($color_blue)
    ->setTextAlign(TextBox::ALIGN_RIGHT)
    ->setXY(20, 0)
    ->setWidth(650)
    ->setBorderTop($solidLine)
    ->setFontStyle($style)
    ->setPadding(5);

//-- Master --
$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
$data = [];
$lorem_len = mb_strlen($lorem);
$group = 'A';
for($i = 0; $i < 10; ++$i) {
    $data[] = ['group' => $group, 'description' =>  mb_substr($lorem, 0, rand(10, $lorem_len))];
    if (rand(0,1) > 0.5) {
        ++$group;
    }
}
$masterDataSet = new ArrayDataSet($data, 'ds1');
$master = new DataBand($page);
$master->setDataSource($masterDataSet);

$text = new TextBox($master, '[ds1.description]');
$text
    ->setFontStyle($style)
    ->setXY(40, 0)
    ->setPadding(5)
    ->setBackgroundColor($color_green)
    ->setBorderTop($solidLine)
    ->setWidth(610);

//-- Group data header --
$groupHeader = new GroupHeader($page);
$groupHeader
    ->setGroupFieldName('group')
    ->setDataSource($masterDataSet);
$text = new TextBox($groupHeader, '[ds1.group]');
$text
    ->setFontStyle($style)
    ->setTextAlign(TextBox::ALIGN_CENTER)
    ->setTextVerticalAlign(TextBox::ALIGN_VERTICAL_CENTER)
    ->setXY(20, 0)
    ->setWidth(50)
    ->setHeight(50)
    ->setBackgroundColor($color_gray);

$master->addBand($groupHeader);

//-- Group data footer --
$groupFooter = new GroupFooter($page);
$groupFooter
    ->setGroupFieldName('group')
    ->setDataSource($masterDataSet);
$text = new TextBox($groupFooter, 'Footer [ds1.group]');
$text
    ->setFontStyle($style)
    ->setTextAlign(TextBox::ALIGN_CENTER)
    ->setTextVerticalAlign(TextBox::ALIGN_VERTICAL_CENTER)
    ->setXY(20, 0)
    ->setHeight(50)
    ->setBackgroundColor($color_gray);

$master->addBand($groupFooter);



?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link type="text/css" rel="stylesheet" href="vendor/cofaho/report/src/Report/Renderer/Html/assets/report.css">
    <style>
        <?=ReportRenderer::getStyle($report)?>
    </style>
</head>
<body>
<?php ReportRenderer::render($report, new EchoWriter()); ?>
</body>
</html>


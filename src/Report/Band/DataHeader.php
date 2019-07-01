<?php

namespace Report\Band;


use Report\Band\Property\PageBreakAfter;
use Report\Band\Property\PageBreakAfterInterface;
use Report\Band\Property\PageBreakBefore;
use Report\Band\Property\PageBreakBeforeInterface;
use Report\Band\Property\PrintOnEveryPage;
use Report\Property\OnOnePage;
use Report\Property\OnOnePageInterface;

class DataHeader extends AbstractBand implements PageBreakBeforeInterface, PageBreakAfterInterface, OnOnePageInterface
{
    use PageBreakBefore, PageBreakAfter, PrintOnEveryPage, OnOnePage;
}

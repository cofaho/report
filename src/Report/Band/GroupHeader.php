<?php

namespace Report\Band;


use Report\Band\Property\Group;
use Report\Band\Property\PageBreakAfter;
use Report\Band\Property\PageBreakAfterInterface;
use Report\Band\Property\PageBreakBefore;
use Report\Band\Property\PageBreakBeforeInterface;
use Report\Band\Property\PrintOnEveryPage;
use Report\Property\OnOnePage;
use Report\Property\OnOnePageInterface;

class GroupHeader extends AbstractBand implements PageBreakBeforeInterface, PageBreakAfterInterface, OnOnePageInterface
{
    use PageBreakBefore, PageBreakAfter, PrintOnEveryPage, OnOnePage, Group;

    /**
     * @return string
     */
    public function isGroupFieldChanged(): string
    {
        $ds = $this->getDataSource();
        if (!$ds) {
            return false;
        }
        $currentValue = $ds->field($this->groupFieldName);
        $hasChanged = $this->groupValue != $currentValue;
        $this->groupValue = $currentValue;
        return $hasChanged;
    }


}

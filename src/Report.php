<?php

namespace Report;


use Serializable;

class Report implements Serializable
{
    const UNITS_MM = 'mm';

    const UNITS_CM = 'cm';

    const UNITS_PX = 'px';

    const UNITS_PT = 'pt';

    const UNITS_IN = 'in';

    /**
     * @var PageTemplate[]
     */
    protected $pages = [];
    /**
     * @var string
     */
    protected $userUnits;

    public function __construct($userUnits = self::UNITS_MM)
    {
        $this->userUnits = $userUnits;
    }

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize([
            $this->userUnits,
            $this->pages
        ]);
    }

    /**
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        list(
            $this->userUnits,
            $this->pages
        ) = unserialize($serialized);
    }

    public function addPage(PageTemplate $page)
    {
        $this->pages[] = $page;
    }

    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @return string
     */
    public function getUserUnits(): string
    {
        return $this->userUnits;
    }
}

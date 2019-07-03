<?php

namespace Report;


use Exception;
use Serializable;

class PageTemplate extends AbstractBandContainer implements Serializable
{
    /**
     * @var float
     */
    protected $height;
    /**
     * @var Report
     */
    protected $report;
    /**
     * @var int
     */
    protected $dpi = 96;


    public function __construct(Report $report = null)
    {
        if ($report) {
            $this->report = $report;
            $report->addPage($this);
        }
    }

    /**
     * @return string|null
     */
    public function serialize()
    {
        return serialize([
            $this->width,
            $this->height,
            $this->margin,
            $this->bands
        ]);
    }

    /**
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        list(
            $this->width,
            $this->height,
            $this->margin,
            $this->bands
        ) = unserialize($serialized);
    }

    /**
     * @param float[]|string $format
     * @param int $orientation
     * @return $this
     * @throws Exception
     */
    public function setFormat($format, $orientation = Page::ORIENTATION_PORTRAIT)
    {
        if (is_string($format) && $this->report) {
            if (!isset(Page::DIMENSIONS[$format])) {
                throw new Exception("Unknown page format `$format`");
            }
            $format = Page::getFormatInUnits(Page::DIMENSIONS[$format], $this->report->getUserUnits(), $this->getDpi());
        }
        if (!is_array($format)) {
            throw new Exception("Wrong page format `$format`");
        }

        $this->setWidth($format[$orientation]);
        $this->setHeight($format[1 - $orientation]);

        return $this;
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight(float $height): void
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getDpi(): int
    {
        return $this->dpi;
    }

    /**
     * @param int $dpi
     * @return $this
     */
    public function setDpi(int $dpi)
    {
        $this->dpi = $dpi;
        return $this;
    }

}

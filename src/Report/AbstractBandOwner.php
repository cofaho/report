<?php

namespace Report;


use Report\Band\BandInterface;

abstract class AbstractBandOwner
{
    /**
     * @var BandInterface[]
     */
    protected $bands = [];

    /**
     * @param BandInterface $band
     * @return $this
     */
    public function addBand(BandInterface $band)
    {
        $this->bands[] = $band;
        return $this;
    }

    /**
     * @param null|string $type
     * @return null|BandInterface[]
     */
    public function getBands($type = null)
    {
        if ($type === null) {
            return $this->bands;
        }
        return array_filter($this->bands, function (BandInterface $band) use ($type){
            return $band instanceof $type;
        });
    }

    /**
     * @param string $type
     * @return BandInterface|null
     */
    public function getBand($type)
    {
        foreach ($this->bands as $band) {
            if ($band instanceof $type) {
                return $band;
            }
        }

        return null;
    }

}

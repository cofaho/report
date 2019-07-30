<?php

namespace Report\Helper;


class Color
{
    /**
     * @var int
     */
    protected $color = 0;
    /**
     * In percents
     * @var float
     */
    protected $alfa = 100;

    public function __construct()
    {
        $args = func_get_args();
        switch (func_num_args()) {
            case 0:
                return;
            case 1:
                $a = $args[0];
                if (is_string($a) && $a[0] == '#') {
                    if (strlen($a) === 4) {
                        $this->color = hexdec($a[1] . $a[1] . $a[2] . $a[2] . $a[3] . $a[3]);
                    } elseif (strlen($a) === 7) {
                        $this->color = hexdec(substr($a, 1));
                    } elseif (strlen($a) === 9) {
                        $this->color = hexdec(substr($a, 1, 6));
                        $this->alfa = round(hexdec(substr($a, 7)) * 100 / 255);
                    }
                }
                break;
            case 4:
                $this->alfa = $args[3];
            case 3:
                $this->color = ($args[0] << 16) + ($args[1] << 8) + $args[2];
                break;
        }
    }

    /**
     * @return array
     */
    public function toNormalizedArray(): array
    {
        return [$this->getR() / 255, $this->getG() / 255, $this->getB() / 255];
    }

    /**
     * @param int $alfa
     * @return $this
     */
    public function setAlfa($alfa)
    {
        $this->alfa = $alfa;
        return $this;
    }

    /**
     * @return int
     */
    public function getAlfa()
    {
        return $this->alfa;
    }

    /**
     * @param int $b
     * @return $this
     */
    public function setB($b)
    {
        $this->color = ($this->color & 0xFFFF00) | $b;
        return $this;
    }

    /**
     * @return int
     */
    public function getB()
    {
        return $this->color & 0x0000FF;
    }

    /**
     * @param int $g
     * @return $this
     */
    public function setG($g)
    {
        $this->color = ($this->color & 0xFF00FF) | ($g << 8);
        return $this;
    }

    /**
     * @return int
     */
    public function getG()
    {
        return ($this->color >> 8) & 0x00FF;
    }

    /**
     * @param int $r
     * @return $this
     */
    public function setR($r)
    {
        $this->color = ($this->color & 0x00FFFF) | ($r << 16);
        return $this;
    }

    /**
     * @return int
     */
    public function getR()
    {
        return $this->color >> 16;
    }

    /**
     * @return int
     */
    public function getColor(): int
    {
        return $this->color;
    }

    /**
     * @param int $color
     * @return $this
     */
    public function setColor(int $color)
    {
        $this->color = $color;
        return $this;
    }


}

<?php

namespace Report\Renderer;


class RenderResult
{
    /**
     * @var mixed
     */
    public $content;
    /**
     * @var mixed
     */
    public $tailObject;
    /**
     * @var mixed
     */
    public $extra;

    public function __construct($content = null, $tailObject = null, $extra = null)
    {
        $this->content = $content;
        $this->tailObject = $tailObject;
        $this->extra = $extra;
    }
}

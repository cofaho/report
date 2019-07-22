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

    public function __construct($content = null, $tailObject = null)
    {
        $this->content = $content;
        $this->tailObject = $tailObject;
    }
}

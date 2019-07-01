<?php

namespace Report\Renderer\Html;


class RenderResult
{
    /**
     * @var string
     */
    public $content;
    /**
     * @var mixed
     */
    public $tailObject;

    public function __construct(string $content = '', $tailObject = null)
    {
        $this->content = $content;
        $this->tailObject = $tailObject;
    }
}

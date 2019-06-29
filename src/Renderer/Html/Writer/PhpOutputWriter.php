<?php

namespace Report\Renderer\Html\Writer;


class PhpOutputWriter implements WriterInterface
{
    protected $handle;

    public function __construct()
    {
        $this->handle = fopen('php://output', 'w');
    }

    public function __destruct()
    {
        fclose($this->handle);
    }

    public function write($data)
    {
        fwrite($this->handle, $data);
    }
}

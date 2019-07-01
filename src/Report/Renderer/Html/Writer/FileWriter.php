<?php

namespace Report\Renderer\Html\Writer;


class FileWriter implements WriterInterface
{
    protected $handle;

    public function __construct($fileName)
    {
        $this->handle = fopen($fileName, 'w+');
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

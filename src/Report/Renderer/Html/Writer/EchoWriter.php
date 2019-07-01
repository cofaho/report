<?php

namespace Report\Renderer\Html\Writer;


class EchoWriter implements WriterInterface
{

    public function write($data)
    {
        echo $data;
    }
}

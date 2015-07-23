<?php

namespace PhpBuf\IO\Writer;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
interface WriterInterface
{

    public function writeByte($byte);

    public function writeBytes($bytes);

    public function getPosition();

    public function redo();

    public function getLength();

    public function getData();
}

<?php

namespace PhpBuf\WireType;

use PhpBuf\IO\Reader\ReaderInterface,
    PhpBuf\IO\Writer\WriterInterface;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
interface WireTypeInterface
{

    public static function read(ReaderInterface $reader);

    public static function write(WriterInterface $writer, $value);
}

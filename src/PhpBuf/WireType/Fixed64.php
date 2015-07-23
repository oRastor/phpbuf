<?php

namespace PhpBuf\WireType;

use PhpBuf\IO\Reader\ReaderInterface,
    PhpBuf\IO\Writer\WriterInterface;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
class Fixed64 implements WireTypeInterface
{

    public static function read(ReaderInterface $reader)
    {
        throw new PhpBuf\NotImplemented\Exception("reader for PhpBuf\WireType\Fixed64 not implemented");
    }

    public static function write(WriterInterface $writer, $data)
    {
        throw new PhpBuf\NotImplemented\Exception("writer for PhpBuf\WireType\Fixed64 not implemented");
    }

}

<?php

namespace PhpBuf\WireType;

use PhpBuf\IO\Reader\ReaderInterface,
    PhpBuf\IO\Writer\WriterInterface,
    PhpBuf\Base128;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
class Varint implements \PhpBuf\WireType\WireTypeInterface
{

    public static function read(ReaderInterface $reader)
    {
        return Base128::decodeFromReader($reader);
    }

    public static function write(WriterInterface $writer, $value)
    {
        Base128::encodeToWriter($writer, $value);
    }

}

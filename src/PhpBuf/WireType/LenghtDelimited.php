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
class LenghtDelimited implements WireTypeInterface
{

    public static function read(ReaderInterface $reader)
    {
        $lenght = Base128::decodeFromReader($reader);
        return $reader->getBytes($lenght);
    }

    public static function write(WriterInterface $writer, $value)
    {
        Base128::encodeToWriter($writer, strlen($value));
        $writer->writeBytes($value);
    }

}

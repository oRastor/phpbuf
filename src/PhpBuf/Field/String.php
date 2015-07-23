<?php

namespace PhpBuf\Field;

use PhpBuf\IO\Reader\ReaderInterface,
    PhpBuf\IO\Writer\WriterInterface,
    PhpBuf\WireType;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
class String extends AbstractField
{

    protected $wireType = WireType::WIRETYPE_LENGTH_DELIMITED;

    protected function readImpl(ReaderInterface $reader)
    {
        return $this->readWireTypeData($reader);
    }

    protected function writeImpl(WriterInterface $writer, $value)
    {
        $this->writeWireTypeData($writer, $value);
    }

    protected function checkTypeOfValueImpl($value)
    {
        return is_string($value);
    }

}

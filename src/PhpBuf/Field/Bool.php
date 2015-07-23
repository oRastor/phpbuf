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
class Bool extends AbstractField
{

    protected $wireType = WireType::WIRETYPE_VARINT;

    protected function readImpl(ReaderInterface $reader)
    {
        return (boolean) $this->readWireTypeData($reader);
    }

    protected function writeImpl(WriterInterface $writer, $value)
    {
        $this->writeWireTypeData($writer, (integer) $value);
    }

    protected function checkTypeOfValueImpl($value)
    {
        return is_bool($value);
    }

}

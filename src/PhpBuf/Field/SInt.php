<?php

namespace PhpBuf\Field;

use PhpBuf\IO\Reader\ReaderInterface,
    PhpBuf\IO\Writer\WriterInterface,
    PhpBuf\WireType,
    PhpBuf\ZigZag;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
class SInt extends AbstractField
{

    protected $wireType = WireType::WIRETYPE_VARINT;

    protected function readImpl(ReaderInterface $reader)
    {
        return ZigZag::decode($this->readWireTypeData($reader));
    }

    protected function writeImpl(WriterInterface $writer, $value)
    {
        $this->writeWireTypeData($writer, ZigZag::encode($value));
    }

    protected function checkTypeOfValueImpl($value)
    {
        return is_integer($value);
    }

}

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
class Int extends AbstractField
{

    protected $wireType = WireType::WIRETYPE_VARINT;

    /**
     * Enter description here...
     *
     * @param PhpBuf\IO\Reader\ReaderInterface $reader
     * @return unknown
     */
    protected function readImpl(ReaderInterface $reader)
    {
        return $this->readWireTypeData($reader);
    }

    /**
     * Enter description here...
     *
     * @param PhpBuf\IO\Writer\WriterInterface $writer
     * @param unknown_type $value
     */
    protected function writeImpl(WriterInterface $writer, $value)
    {
        $this->writeWireTypeData($writer, $value);
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $value
     * @return unknown
     */
    protected function checkTypeOfValueImpl($value)
    {
        return is_integer($value);
    }

}

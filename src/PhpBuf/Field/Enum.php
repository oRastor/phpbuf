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
class Enum extends AbstractField
{

    protected $wireType = WireType::WIRETYPE_VARINT;

    protected function readImpl(ReaderInterface $reader)
    {
        if (false === $this->value) {
            throw new Exception("Unknow value in enum");
        }
        return $this->getEnumNameById($this->readWireTypeData($reader));
    }

    protected function writeImpl(WriterInterface $writer, $value)
    {
        $value = $this->getEnumIdByName($value);
        if (false === $value) {
            throw new Exception("Unknow value in enum");
        }
        $this->writeWireTypeData($writer, $value);
    }

    protected function getEnumNameById($id)
    {
        if (isset($this->extra[$id])) {
            return $this->extra[$id];
        }
        return false;
    }

    protected function getEnumIdByName($name)
    {
        $enums = array_flip($this->extra);
        if (isset($enums[$name])) {
            return $enums[$name];
        }
        return false;
    }

    protected function checkTypeOfValueImpl($value)
    {
        $enums = array_flip($this->extra);
        return isset($enums[$value]);
    }

}

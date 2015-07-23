<?php

namespace PhpBuf\Field;

use PhpBuf\IO\Reader\ReaderInterface,
    PhpBuf\IO\Writer\WriterInterface,
    PhpBuf\IO\Reader,
    PhpBuf\IO\Writer,
    PhpBuf\WireType;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
class Message extends AbstractField
{

    protected static $reflectObjectCache = array();
    protected static $reflectClass = array();
    protected $wireType = WireType::WIRETYPE_LENGTH_DELIMITED;

    protected function readImpl(ReaderInterface $reader, $repeatable = false)
    {
        $bytes = $this->readWireTypeData($reader);
        $refClass = self::getReflectClass($this->extra);
        $message = $refClass->newInstance();
        $message->read(new Reader($bytes));
        return $message;
    }

    protected function writeImpl(WriterInterface $writer, $value)
    {
        $newWriter = new Writer();
        $value->write($newWriter);
        $this->writeWireTypeData($writer, $newWriter->getData());
    }

    protected function checkTypeOfValueImpl($value)
    {
        $refObject = self::getReflectObject($value);
        $messageName = $refObject->getMethod('name')->invoke($value);
        if ($this->extra === $messageName) {
            return true;
        }
        $refClass = self::getReflectClass($this->extra);
        return $refClass->isInstance($value);
    }

    /**
     * @param object $value
     * @return ReflectionObject
     */
    protected static function getReflectObject($value)
    {
        $hash = spl_object_hash($value);
        if (isset(self::$reflectObjectCache[$hash])) {
            return self::$reflectObjectCache[$hash];
        }
        return self::$reflectObjectCache[$hash] = new \ReflectionObject($value);
    }

    /**
     * @param string $className
     * @return reflectionClass
     */
    protected static function getReflectClass($className)
    {
        if (isset(self::$reflectClass[$className])) {
            return self::$reflectClass[$className];
        }
        return self::$reflectClass[$className] = new \ReflectionClass($className);
    }

}

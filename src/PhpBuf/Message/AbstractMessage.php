<?php

namespace PhpBuf\Message;

use PhpBuf\IO\Reader\ReaderInterface,
    PhpBuf\IO\Writer\WriterInterface,
    PhpBuf\Base128;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
abstract class AbstractMessage implements MessageInterface
{

    /**
     *  Fields array of message
     *
     * @var array
     */
    protected $fields = array();

    /**
     *  Index to name transformation table
     *
     * @var array
     */
    protected $indexToName = array();

    /**
     * Name to index transformation table
     *
     * @var array
     */
    protected $nameToIndex = array();

    /**
     * Dynamic getter
     *
     * @param string $field
     * @return mixed
     */
    public function __get($field)
    {
        return $this->getValue($field);
    }

    /**
     * Dynamic setter
     *
     * @param string $field
     * @param mixed $value
     */
    public function __set($field, $value)
    {
        $this->setValue($field, $value);
    }

    /**
     * Read message from reader
     *
     * @param IO\Reader\ReaderInterface $reader
     * @param boolean $strict
     */
    public function read(ReaderInterface $reader, $strict = true)
    {
        try {
            if ($strict) {
                $this->strictRead($reader);
            } else {
                $this->laxRead($reader);
            }
        } catch (PhpBuf_IO_Exception $e) {
            return;
        }
    }

    public static function readArray(ReaderInterface $reader, $messageClassName, $strict = true)
    {
        $result = array();

        while ($reader->getPosition() < $reader->getLength()) {
            $length = Base128::decodeFromReader($reader);

            $messageData = $reader->getBytes($length);

            $messageReader = new \PhpBuf\IO\Reader($messageData);

            $message = new $messageClassName();
            $message->read($messageReader, $strict);

            array_push($result, $message);
        }

        return $result;
    }

    public static function writeArray(WriterInterface $writer, $messages)
    {
        foreach ($messages as $item) {
            $itemWriter = new \PhpBuf\IO\Writer();
            $item->write($itemWriter);

            $length = $itemWriter->getLength();

            Base128::encodeToWriter($writer, $length);
            $writer->writeBytes($itemWriter->getData());
        }
    }

    public function toArray()
    {
        $result = array();
        foreach ($this->fields as $field) {
            $key = $this->indexToName[$field->getIndex()];
            if ($field instanceof \PhpBuf\Field\Message) {
                if ($field->getValue() === null) {
                    $result[$key] = $field->getValue();
                } else {
                    $result[$key] = $field->getValue()->toArray();
                }
            } else {
                $result[$key] = $field->getValue();
            }
        }

        return $result;
    }

    private function getMessageLength(ReaderInterface $reader)
    {
        return Base128::decodeFromReader($reader);
    }

    /**
     * Write message to writer
     *
     * @param IO\Writer\Interface $writer
     */
    public function write(WriterInterface $writer)
    {
        foreach ($this->fields as $field) {
            $field->write($writer);
        }
    }

    /**
     * Enter description here...
     *
     * @param string $name
     * @param integer $type
     * @param integer $rule
     * @param integer $index
     * @param mixed $extra
     */
    protected function setField($name, $type, $rule, $index, $extra = '')
    {
        if (\PhpBuf\Type::MESSAGE === $type && (!is_string($extra) || empty($extra))) {
            throw new Exception('message mast have $extra in file:' . $name);
        }
        if (\PhpBuf\Type::ENUM === $type && (!is_array($extra) || empty($extra))) {
            throw new Exception('enum mast have $extra');
        }
        $fieldClass = \PhpBuf\Field\AbstractField::create($type, array('index' => $index, 'rule' => $rule, 'extra' => $extra));

        $this->fields[$index] = $fieldClass;
        $this->nameToIndex[$name] = $index;
        $this->indexToName[$index] = $name;
    }

    /**
     * Helper function for dynamic getter
     *
     * @param string $field
     * @param boolean $throwException
     * @return mixed
     */
    protected function getValue($field, $throwException = true)
    {
        if (isset($this->nameToIndex[$field])) {
            $fieldClass = $this->fields[$this->nameToIndex[$field]];
            return $fieldClass->getValue();
        }
        if ($throwException) {
            throw new Exception("property $field not found");
        }
    }

    /**
     * Helper function for dynamic setter
     *
     * @param string $field
     * @param mixed $value
     * @param boolean $throwException
     */
    protected function setValue($field, $value, $throwException = true)
    {
        if (isset($this->nameToIndex[$field])) {
            $fieldClass = $this->fields[$this->nameToIndex[$field]];
            $fieldClass->setValue($value);
            return;
        }
        if ($throwException) {
            throw new Exception("property $field not found");
        }
    }

    /**
     * Read only the correct message 
     *
     * @param IO\Reader\ReaderInterface $reader
     */
    protected function strictRead(ReaderInterface $reader)
    {
        while ($reader->getPosition() < $reader->getLength()) {
            $fieldClass = $this->readFieldFromHeader($reader);
            $fieldClass->read($reader);
        }
    }

    /**
     * Read the message in disregard of unknown fields
     *
     * @param IO\Reader\ReaderInterface $reader
     */
    protected function laxRead(ReaderInterface $reader)
    {
        while ($reader->getPosition() < $reader->getLength()) {
            try {
                $fieldClass = $this->readFieldFromHeader($reader);
                $fieldClass->read($reader);
            } catch (PhpBuf\Field\NotFoundException $e) {
                
            }
        }
    }

    /**
     * Read field info from reader and return associated field class
     *
     * @param IO\Reader\ReaderInterface $reader
     * @return Message\AbstractMessage
     */
    protected function readFieldFromHeader(ReaderInterface $reader)
    {
        $varint = Base128::decodeFromReader($reader);
        $fieldIndex = $varint >> 3;
        $wireType = self::mask($varint);
        if (!isset($this->fields[$fieldIndex])) {
            throw new PhpBuf\Field\NotFoundException("class " . get_class($this) . " field index $fieldIndex not found");
        }
        $fieldClass = $this->fields[$fieldIndex];
        $fieldsWireType = $fieldClass->getWireType();
        if ($wireType !== $fieldsWireType) {
            throw new PhpBuf\Field\Exception("discrepancy of wire types");
        }
        return $fieldClass;
    }

    protected static function mask($varint)
    {
        static $bigMask = null;
        if (null === $bigMask) {
            // cache
            $bigMask = bindec('111');
        }
        return $varint & $bigMask;
    }

}

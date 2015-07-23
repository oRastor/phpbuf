<?php

namespace PhpBuf\IO;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
class Writer implements Writer\WriterInterface
{

    protected $data = "";
    protected $position = 0;
    protected $lastLenght = 0;

    public function writeByte($byte)
    {
        $this->lastLenght = 1;
        if (strlen($byte) > 1) {
            throw new PhpBuf\IO\Exception("lenght too big");
        }
        $this->data .= $byte;
    }

    public function writeBytes($bytes)
    {
        $this->lastLenght = strlen($bytes);
        $this->data .= $bytes;
        $this->position = $this->getLength();
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function redo()
    {
        $lastRecord = substr($this->data, -($this->lastLenght));
        $this->data = substr($this->data, 0, -($this->lastLenght));
        $this->position = $this->position - $this->lastLenght;
        return $lastRecord;
    }

    public function getLength()
    {
        if (is_array($this->data)) {
            $this->data = implode('', $this->data);
        }
        return strlen($this->data);
    }

    public function getData()
    {
        return $this->data;
    }

}

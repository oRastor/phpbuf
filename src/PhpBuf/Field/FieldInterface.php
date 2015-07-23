<?php

namespace PhpBuf\Field;

use PhpBuf\IO\Reader\ReaderInterface,
    PhpBuf\IO\Writer\WriterInterface;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
interface FieldInterface {

    public function read(ReaderInterface $reader);

    public function write(WriterInterface $writer);

    public function setValue($value);

    public function getValue();
}

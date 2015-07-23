<?php

namespace PhpBuf\IO\Reader;

use PhpBuf\IO\Writer\WriterInterface;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
interface ReaderInterface
{

    public static function createFromWriter(WriterInterface $writer);

    public function getByte();

    public function getBytes($lengnt = 1);

    public function setPosition($position = 0);

    public function getPosition();

    public function next($steps = 1);

    public function redo();

    public function getLength();
}

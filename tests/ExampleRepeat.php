<?php

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
class PhpBuf_Message_ExampleRepeat extends PhpBuf\Message\AbstractMessage
{

    public function __construct()
    {
        $this->setField("messages", PhpBuf\Type::MESSAGE, PhpBuf\Rule::REPEATED, 1, "PhpBuf_Message_Example");
        $this->setField("name", PhpBuf\Type::STRING, PhpBuf\Rule::REPEATED, 2);
    }

    public static function name()
    {
        return __CLASS__;
    }

}

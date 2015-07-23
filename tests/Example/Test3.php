<?php

class PhpBuf_Message_Example_Test3 extends PhpBuf\Message\AbstractMessage
{

    public function __construct()
    {
        $this->setField("c", PhpBuf\Type::MESSAGE, PhpBuf\Rule::REQUIRED, 3, "PhpBuf_Message_Example_Test1");
    }

    public static function name()
    {
        return __CLASS__;
    }

}

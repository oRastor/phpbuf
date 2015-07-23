<?php

class PhpBuf_Message_Example_Test1 extends PhpBuf\Message\AbstractMessage
{

    public function __construct()
    {
        $this->setField("a", PhpBuf\Type::INT, PhpBuf\Rule::REQUIRED, 1);
    }

    public static function name()
    {
        return __CLASS__;
    }

}

<?php

class PhpBuf_Message_Example_Test2 extends PhpBuf\Message\AbstractMessage
{

    public function __construct()
    {
        $this->setField("b", PhpBuf\Type::STRING, PhpBuf\Rule::REQUIRED, 2);
    }

    public static function name()
    {
        return __CLASS__;
    }

}

?>
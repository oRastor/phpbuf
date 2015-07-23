<?php

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
class PhpBuf_Message_Example extends PhpBuf\Message\AbstractMessage
{

    public function __construct()
    {
        $this->setField("id", PhpBuf\Type::INT, PhpBuf\Rule::REQUIRED, 1);
        $this->setField("balance", PhpBuf\Type::SINT, PhpBuf\Rule::REQUIRED, 2);
        $this->setField("isAdmin", PhpBuf\Type::BOOL, PhpBuf\Rule::REQUIRED, 3);
        $this->setField("status", PhpBuf\Type::ENUM, PhpBuf\Rule::REQUIRED, 4, array("active", "inactive", "deleted"));
        $this->setField("name", PhpBuf\Type::STRING, PhpBuf\Rule::REQUIRED, 5);
        $this->setField("bytes", PhpBuf\Type::BYTES, PhpBuf\Rule::REQUIRED, 6);
    }

    public static function name()
    {
        return __CLASS__;
    }

}

?>
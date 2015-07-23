<?php

namespace PhpBuf\RPC\Message;

class Response extends \PhpBuf\Message\AbstractMessage
{

    public function __construct()
    {
        $this->setField('responseProto', PhpBuf\Type::BYTES, PhpBuf\Rule::OPTIONAL, 1);
        $this->setField('error', PhpBuf\Type::STRING, PhpBuf\Rule::OPTIONAL, 2);
        $this->setField('callback', PhpBuf\Type::BOOL, PhpBuf\Rule::OPTIONAL, 3);
        $this->setField('errorReason', PhpBuf\Type::ENUM, PhpBuf\Rule::OPTIONAL, 4, PhpBuf\RPC\Message\ErrorReason::values());
    }

    public static function name()
    {
        return __CLASS__;
    }

}

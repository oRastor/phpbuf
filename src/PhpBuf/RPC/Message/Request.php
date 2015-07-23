<?php

namespace PhpBuf\RPC\Message;

class Request extends \PhpBuf\Message\AbstractMessage
{

    protected $requestMessage;

    public function __construct()
    {
        $this->setField('serviceName', PhpBuf\Type::STRING, PhpBuf\Rule::REQUIRED, 1);
        $this->setField('methodName', PhpBuf\Type::STRING, PhpBuf\Rule::REQUIRED, 2);
        $this->setField('requestProto', PhpBuf\Type::BYTES, PhpBuf\Rule::REQUIRED, 3);
    }

    public function setRequestMessage(PhpBuf\Message\AbstractMessage $message)
    {
        $this->requestMessage = $message;
    }

    public function getRequestMessage()
    {
        return $this->requestMessage;
    }

    public static function name()
    {
        return __CLASS__;
    }

}

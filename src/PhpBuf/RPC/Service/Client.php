<?php

namespace PhpBuf\RPC\Service;

//
// using: http://code.google.com/p/protobuf-socket-rpc/
//
abstract class Client
{

    /**
     * @var PhpBuf\RPC\Balancer\Interface
     */
    protected $balancer;

    /**
     * @var string
     */
    protected $serviceFullQualifiedName = '';

    /**
     * @var array
     */
    protected $registerMethodResponderClasses = array();

    /**
     * @param PhpBuf\RPC\Context $context
     * @param PhpBuf\RPC\Socket\Factory $factory
     */
    public function __construct(\PhpBuf\RPC\Context $context, \PhpBuf\RPC\Socket\Factory $factory = null)
    {
        if (null === $factory) {
            $factory = new \PhpBuf\RPC\Socket\Factory;
        }
        $this->balancer = new \PhpBuf\RPC\Balancer\Random($context, $factory);
    }

    protected function setServiceFullQualifiedName($serviceFullQualifiedName)
    {
        $this->serviceFullQualifiedName = $serviceFullQualifiedName;
    }

    protected function registerMethodResponderClass($methodName, $className)
    {
        $this->registerMethodResponderClasses[$methodName] = $className;
    }

    protected function getMethodResponderClass($methodName)
    {
        return $this->registerMethodResponderClasses[$methodName];
    }

    /**
     * @param PhpBuf_RPC_Message_Request $request
     * @return PhpBuf_RPC_Message_Response
     */
    public function request(PhpBuf\RPC\Message\Request $request)
    {
        try {
            $writer = new PhpBuf\IO\Writer;
            $request->write($writer);

            $socket = $this->balancer->get();
            $socket->write($writer->getData(), $writer->getLength());
            $socket->shutdownWrite();

            $resultData = $socket->read(4096);
            $socket->shutdownRead();
            $socket->close();

            $response = new PhpBuf\RPC\Message\Response;
            $response->read(new PhpBuf\IO\Reader($resultData));
            return $response;
        } catch (PhpBuf\RPC\Socket\Exception $e) {
            throw new PhpBuf\RPC\Exception($e->getMessage(), PhpBuf\RPC\Message\ErrorReason::IO_ERROR);
        }
    }

    public function callRPC($serviceName, $methodName, PhpBuf\Message\AbstractMessage $requestMessage, $responderClassName)
    {
        $writer = new PhpBuf_IO_Writer;
        $requestMessage->write($writer);

        $request = new PhpBuf_RPC_Message_Request;
        $request->serviceName = $serviceName;
        $request->methodName = $methodName;
        $request->requestProto = $writer->getData();
        $response = $this->request($request);
        if (null !== $response->error && null !== $response->errorReason) {
            throw new PhpBuf\RPC\Exception($response->error, $response->errorReason);
        }

        $instance = new $responderClassName;
        $instance->read(new PhpBuf\IO\Reader($response->responseProto));
        return $instance;
    }

    public function callMethod($methodName, PhpBuf\Message\AbstractMessage $requestMessage)
    {
        $responderClassName = $this->getMethodResponderClass($methodName);
        return $this->callRPC($this->serviceFullQualifiedName, $methodName, $requestMessage, $responderClassName);
    }

    public function __call($methodName, array $args = array())
    {
        if (empty($args)) {
            throw new InvalidArgumentException('arguments was empty');
        }
        return $this->callMethod($methodName, $args[0]);
    }

}

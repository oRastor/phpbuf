<?php

namespace PhpBuf\RPC\Balancer;

class Random implements BalancerInterface
{

    /**
     * @var PhpBuf\RPC\Context
     */
    protected $context;

    /**
     * @var PhpBuf\RPC\Socket\Factory
     */
    protected $factory;

    /**
     * @param PhpBuf\RPC\Context $context
     */
    public function __construct(PhpBuf\RPC\Context $context, PhpBuf\RPC\Socket\Factory $factory)
    {
        $this->context = $context;
        $this->factory = $factory;
    }

    /**
     * @return PhpBuf\RPC\Socket\Interface
     */
    public function get()
    {
        $copy = (array) $this->context->getServers();
        shuffle($copy);
        $count = count($copy);

        $lastException = null;
        for ($i = 0; $i < $count; ++$i) {
            $server = $copy[$i];
            try {
                return $this->factory->create($server['host'], $server['port']);
            } catch (PhpBuf\RPC\Socket\Exception $e) {
                $lastException = $e;
                //
                // next server
                // TODO: failover
            //
            }
        }
        if (null != $lastException) {
            throw new PhpBuf\RPC\Socket\Exception($lastException->getMessage(), $lastException->getCode());
        }
    }

}

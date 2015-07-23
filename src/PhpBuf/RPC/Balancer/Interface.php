<?php

namespace PhpBuf\RPC\Balancer;

interface BalancerInterface
{

    /**
     * @return PhpBuf\RPC\Socket\Interface
     */
    public function get();
}

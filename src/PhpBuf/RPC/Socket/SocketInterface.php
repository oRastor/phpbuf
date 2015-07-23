<?php

namespace PhpBuf\RPC\Socket;

interface SocketInterface
{

    const DEFAULT_IPADDR = '127.0.0.1';

    public function read($length = 1024);

    public function write($data, $length = null);

    public function shutdownRead();

    public function shutdownWrite();

    public function shutdown();

    public function close();
}

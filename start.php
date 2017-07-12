<?php
class DnsServer
{
    protected $port = 53;

    protected $ip = "0.0.0.0";

    protected $proxy = null;

    public function __construct(array $config, agent $proxy)
    {
        $this->ip = $config['ip'];
        $this->port = $config['port'];
        $this->proxy = $proxy;
    }


    public function listen()
    {
        $socket = stream_socket_server("udp://0.0.0.0:553", $errno, $errstr, STREAM_SERVER_BIND);
        do {
            echo "start---------",PHP_EOL;
            $pkt = stream_socket_recvfrom($socket, 8192, 0, $peer);
            echo "$pkt---------",PHP_EOL;
            echo "$peer\n";
            echo "start---------",PHP_EOL;
            stream_socket_sendto($socket, date("D M j H:i:s Y\r\n"), 0, $peer);
        } while ($pkt !== false);
    }

}


























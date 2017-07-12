<?php
include "./interface/Agent.php";

class DnsAgent implements Agent
{
    protected $agentIp = NULL;
    protected $agentPort = NULL;
    protected $client = NULL;

    public function __construct($agentIp = "", $agentPort = 53)
    {
        self::checkEnv();
        $this->agentIp = $agentIp;
        $this->agentPort = $agentPort;
        $this->client = new Swoole\Client(SWOOLE_SOCK_UDP);
        if (!$this->client->connect($this->agentIp,$agentPort,1)) {
            die("DNS代理服务器连接失败");
        }
    }

    public function send($data)
    {
        return $this->client->send($data);
    }

    public function recv()
    {
        return $this->client->recv(1024,0);
    }

    public static function checkEnv()
    {
        if (!extension_loaded('swoole')) {
            die("Error: Swoole Extension is not loaded!");
        }
    }


}

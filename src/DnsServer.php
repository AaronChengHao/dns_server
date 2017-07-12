<?php
include "./DnsAgent.php";
include 'plugin/plugin.php';

class DnsServer
{
    protected $listenIp = NULL;

    protected $listenPort = 53;

    protected $server = NULL;

    protected $agent = NULL;

    public function __construct(array $config, Agent $agent)
    {
        self::checkEnv();
        $this->listenIp = $config['listenIp'];
        $this->listenPort = $config['listenPort'];
        $this->agent = $agent;
        //创建dnsServer对象，类型为SWOOLE_SOCK_UDP
        $this->server = new Swoole\Server($this->listenIp, $this->listenPort, SWOOLE_BASE, SWOOLE_SOCK_UDP);
    }

    public function listen()
    {
        // 监听数据包接收事件
        $this->server->on('Packet',[$this,'recv']);
        $this->server->start();
    }

    /**
     * 接收请求处理方法
     */
    public function recv($serv, $data, $clientInfo)
    {
        // 调用请求前插件
        plugin::begin($data,$clientInfo);
        // 转发dns请求数据包给代理对象
        $this->agent->send($data);
        // 从agent对象获取结果
        $responseDns = $this->agent->recv();
        // 调用请求后插件
        plugin::end($responseDns,$data,$clientInfo);
        // 得到的结果回发给客户
        $serv->sendto($clientInfo['address'], $clientInfo['port'], $responseDns);
    }

    public static function checkEnv()
    {
        if (!extension_loaded('swoole')) {
            die("Error: Swoole Extension is not loaded!");
        }
    }
}

$dnsAgent = new DnsAgent('192.168.1.1',53);
$dnsServer = new DnsServer(['listenIp' => '0.0.0.0', 'listenPort' => 53], $dnsAgent);

$dnsServer->listen();

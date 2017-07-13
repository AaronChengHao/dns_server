<?php
namespace Dns;

class Server
{
    protected $listenIp = NULL;

    protected $listenPort = 53;

    protected $server = NULL;

    protected $agent = NULL;

    public static $recvBufferLen = 8192;


    public function __construct(array $config, Agent $agent)
    {
        self::checkEnv();
        $this->listenIp = $config['ip'];
        $this->listenPort = $config['port'];
        $this->agent = $agent;
        // //创建dnsServer对象，类型为SWOOLE_SOCK_UDP
        // $this->server = new Swoole\Server($this->listenIp, $this->listenPort, SWOOLE_BASE, SWOOLE_SOCK_UDP);
    }


    public function listen()
    {
        $socketString = "udp://{$this->listenIp}:{$this->listenPort}";
        $errno = $errstr = null;
        $socket = stream_socket_server($socketString, $errno, $errstr, STREAM_SERVER_BIND);
        do {
            var_dump($socket);
            $buffer = stream_socket_recvfrom($socket, static::$recvBufferLen, 0, $address);
            var_dump($buffer);
            $this->agent->send($buffer);
            var_dump($this->agent->recv());


            $outstring = "接收到数据:{$buffer} ip:{$address}";
            echo iconv('UTF-8', 'GBK', $outstring),PHP_EOL;
            stream_socket_sendto($socket, date("D M j H:i:s Y\r\n"), 0, $address);
        } while ($buffer);

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
        // if (!extension_loaded('swoole')) {
        //     die("Error: Swoole Extension is not loaded!");
        // }
    }
}



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
        //创建dnsServer对象，类型为SWOOLE_SOCK_UDP
        $this->server = new Swoole\Server($this->listenIp, $this->listenPort, SWOOLE_BASE, SWOOLE_SOCK_UDP);
        if (!$this->server) {
            exit('Error: Swoole Server Create Fail!');
        }
    }


    public function listen()
    {
        $socketString = "udp://{$this->listenIp}:{$this->listenPort}";
        $errno = $errstr = null;
        $socket = stream_socket_server($socketString, $errno, $errstr, STREAM_SERVER_BIND);
        do {
            echo "start:------",PHP_EOL;
            $request = stream_socket_recvfrom($socket, static::$recvBufferLen, 0, $address);
            if (empty($request) || empty($address)) {
                continue;
            }
            echo "IP:{$address}\nrequest:{$request}------",PHP_EOL;
            $this->agent->send($request);
            $response = $this->agent->recv();
            echo "response:{$response}------",PHP_EOL;
            $sendLen = stream_socket_sendto($socket, $response, 0, $address);
            echo "send:{$sendLen}------IP:{$address}",PHP_EOL,PHP_EOL,PHP_EOL;
        } while (true);

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

function output($content = ''){
    echo "length:",strlen($content),PHP_EOL;
    echo iconv('UTF-8', 'GBK', $content),PHP_EOL;
}

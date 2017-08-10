<?php
namespace Dns;

use Dns\Agent;
use Dns\Exception\NotFoundException;
use Dns\Request;


class Server
{
    /**
     * 监听ip
     */
    protected $listenIp = '0.0.0.0';

    /**
     * 监听端口
     */
    protected $listenPort = 53;

    /**
     * DNS 服务
     */
    protected $server = NULL;

    /**
     * Dns 客户端代理对象
     */
    protected $agent = NULL;

    public static $recvBufferLen = 8192;


    public function __construct( $config, $agent)
    {
        self::checkEnv();
        $this->listenIp = $config['ip'];
        $this->listenPort = (int)$config['port'];
        $this->agent = $agent;
    }

    /**
     * 开始监听
     */
    public function listen()
    {
        $this->server = new \Swoole\Server($this->listenIp, $this->listenPort, SWOOLE_BASE, SWOOLE_SOCK_UDP);
        $this->server->on('Packet',[$this,'recv']);
        $this->server->start();
    }

    /**
     * 接收dns请求数据
     */
    public function recv($serv, $data, $clientInfo)
    {
        $request = new Request($data);
        echo "请求域名：".$request->getFilterDomain().PHP_EOL;
        if ($this->agent->send($request) > 0){
            $response = $this->agent->recv();
            if (!$response->isEmpty()){
                $this->send($clientInfo, $response->getRawData());
            }
        }

    }

    /**
     * 回送dns解析的数据
     */
    public function send($clientInfo,$data)
    {
        $remoteIp = $clientInfo['address'];
        $port = $clientInfo['port'];
        $this->server->sendto($remoteIp, $port, $data);
    }

    /**
     * swoole 环境检查
     *
     */
    public static function checkEnv()
    {
        if (!extension_loaded('swoole')) {
            throw new NotFoundException("rror: Swoole Extension is not loaded!", 1);
        }
    }
}

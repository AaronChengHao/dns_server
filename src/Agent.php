<?php
namespace Dns;

use Dns\Exception\NotFoundException;
use Dns\Exception\ConnectException;


class Agent
{

    /**
     * 代理ip
     */
    protected $agentIp = NULL;

    /**
     * 代理端口
     */
    protected $agentPort = NULL;

    /**
     * dns客户端
     */
    protected $client = NULL;

    public function __construct(array $config)
    {
        self::checkEnv();
        $this->agentIp = $config['ip'];
        $this->agentPort = $config['port'];
        $this->client = new Swoole\Client(SWOOLE_SOCK_UDP);
        if (!$this->client->connect($this->agentIp, $this->agentPort,1)){
            throw new ConnectException("Error: connect dns server fail!", 1);
        }
    }

    /**
     * 发送数据
     *
     * @param $data 要发送的数据
     * @return 返回发送的字节数
     */
    public function send($data)
    {
        return $this->client->send($data);
    }

    /**
     * 接收数据
     *
     * @return 返回接收数据
     */
    public function recv()
    {
        return $this->client->recv();
    }

    /**
     * 获取代理ip
     */
    public function getAgentIp()
    {
        return $this->agentIp;
    }

    /**
     * 获取代理端口
     */
    public function getAgentPort()
    {
        return $this->agentPort;
    }

    /**
     * 获取dns客户端实例
     */
    public function getClient()
    {
        return $this->client;
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

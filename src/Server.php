<?php
namespace Dns;

use Dns\Agent;
use Dns\Exception\NotFoundException;

class Server
{
    /**
     * ����ip
     */
    protected $listenIp = '0.0.0.0';

    /**
     * �����˿�
     */
    protected $listenPort = 53;

    /**
     * DNS ����
     */
    protected $server = NULL;

    /**
     * Dns �ͻ��˴������
     */
    protected $agent = NULL;

    public static $recvBufferLen = 8192;


    public function __construct(array $config, Agent $agent)
    {
        self::checkEnv();
        $this->listenIp = $config['ip'];
        $this->listenPort = (int)$config['port'];
        $this->agent = $agent;
    }

    /**
     * ��ʼ����
     */
    public function listen()
    {
        $this->server = new Swoole\Server($this->listenIp, $this->listenPort, SWOOLE_BASE, SWOOLE_SOCK_UDP);
        $this->server->on('Packet',[$this,'recv']);
        $this->server->start();
    }

    /**
     * ����dns��������
     */
    public function recv($serv, $data, $clientInfo)
    {
        if ($this->agent->send($data) > 0){
            $msg = $this->agent->recv();
            if ($msg){
                $this->send($clientInfo, $msg);
            }
        }

    }

    /**
     * ����dns����������
     */
    public function send($clientInfo,$data)
    {
        $remoteIp = $clientInfo['address'];
        $port = $clientInfo['port'];
        $this->server->sendto($remoteIp, $port, $data);
    }

    /**
     * swoole �������
     *
     */
    public static function checkEnv()
    {
        if (!extension_loaded('swoole')) {
            throw new NotFoundException("rror: Swoole Extension is not loaded!", 1);
        }
    }
}

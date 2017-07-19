<?php
namespace Dns;

class Agent
{
    protected $agentIp = NULL;
    protected $agentPort = NULL;
    protected $client = NULL;
    protected static $readBufferLen = 8192;

    public function __construct(array $config)
    {
        self::checkEnv();
        $this->agentIp = $config['ip'];
        $this->agentPort = $config['port'];
        try{
            $this->client = new Swoole\Client(SWOOLE_SOCK_UDP);
            if (!$this->client->connect($this->agentIp, $this->agentPort,1)){
                throw new Exception("error: connect dns server fail!", 1);
            }
        }catch (\Exception $e){
            print $e;
        }
    }

    public function send($data)
    {
        return $this->client->send($data);
    }


    public function recv()
    {
        return $this->client->recv();
    }

//    public function __construct(array $config)
//    {
//        self::checkEnv();
//        $this->agentIp = $config['ip'];
//        $this->agentPort = $config['port'];
//        $socketString = "udp://{$this->agentIp}:{$this->agentPort}";
//        $errno = $errstr = null;
//        $this->client = @stream_socket_client($socketString, $errno, $errstr);
//        if (!$this->client) {
//            exit("代理对象连接DNS服务器失败");
//        }
//        stream_set_blocking ($this->client,0);
//    }
//
//    public function send($data)
//    {
//        return fwrite($this->client, $data);
//    }
//
//    public function recv()
//    {
//        return fread($this->client, static::$readBufferLen);
//    }

    public static function checkEnv()
    {
        // if (!extension_loaded('swoole')) {
        //     die("Error: Swoole Extension is not loaded!");
        // }
    }


}

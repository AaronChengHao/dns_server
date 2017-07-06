<?php
class DnsServer
{

    private $dnsAgentIP = null;

    private $dnsAgentPort = 53;

    private $dnsAgentCli = null;

    private $dnsServer = null;

    public function __construct($agent)
    {
        $this->dnsAgent = $agent;
        $this->cliDns();
        $this->serverDns();
    }

    protected function cliDns()
    {
        try {
            $this->dnsAgentCli = new Swoole\Client(SWOOLE_SOCK_UDP);
            if (!$cli->connect($this->agent,$this->dnsAgentPort,1)) {
                 echo "连接DNS代理服务器失败!";
                 exit(5);
             }
        } catch (Exception $e) {
            echo $e;
            throw $e;
        }
    }

    protected function serverDns()
    {
        //创建Server对象，监听 0.0.0.0:9503端口，类型为SWOOLE_SOCK_UDP
        $this->dnsServer = new Swoole\Server("0.0.0.0", 53, SWOOLE_BASE, SWOOLE_SOCK_UDP);
        //监听数据发送事件
        $this->dnsServer->on('Packet', [$this,'']);
        // $this->dnsServer->on('Packet', function ($serv, $data, $clientInfo) use ($cli)  {

        //     echo "----------------begin----------------------",PHP_EOL;
        //     $requestDomain = getName($data);

        //     $responseDns = makeDnsResponse($data);
        //     echo "响应字节：",strlen($responseDns),PHP_EOL;
        //     $serv->sendto($clientInfo['address'], $clientInfo['port'], $responseDns);
        //     return;
        // });
    }

    public function start()
    {
        $this->server();
    }




    public



}



















//创建Server对象，监听 127.0.0.1:9503端口，类型为SWOOLE_SOCK_UDP
$serv = new Swoole\Server("0.0.0.0", 53, SWOOLE_BASE, SWOOLE_SOCK_UDP);

$cli = new Swoole\Client(SWOOLE_SOCK_UDP);
if (!$cli->connect("10.202.72.118",53,1)) {
echo "dns客户端连接dns服务器失败.",PHP_EOL;
exit(5);
}
echo "dns客户端连接dsn服务器成功",PHP_EOL;




//监听数据发送事件
$serv->on('Packet', function ($serv, $data, $clientInfo) use ($cli)  {

    echo "----------------begin----------------------",PHP_EOL;
    $requestDomain = getName($data);

    $responseDns = makeDnsResponse($data);
    echo "响应字节：",strlen($responseDns),PHP_EOL;
    $serv->sendto($clientInfo['address'], $clientInfo['port'], $responseDns);
    return;
});

//启动服务器
$serv->start();

function getId($data){
    return unpack("nid", $data)['id'];
}

function getName($data){
    $domain = substr($data,12, strlen($data)-4);
    $dexStr = '';
    foreach (unpack('c*', $domain) as $key => $value) {
        $dexStr .=  sprintf("%'02X",$value) ;
    }
    echo "查询域名:$domain ------> $dexStr",PHP_EOL;
    return $domain;
}


function makeDsnRequest($id = 0)
{
    $domain = 'www.qq.com.';
    // pack('n6', ID, QR-RCODE, QDCOUNT, ANCOUNT, NSCOUNT, ARCOUNT)
    $data = pack('n6', $id, 0x0100, 1, 0, 0, 0);
    // QNAME
    foreach (explode('.', $domain) as $bit) {
        $l = strlen($bit);
        $data .= chr($l) . $bit;
    }
    // pack('n2', QTYPE, QCLASS)
    $data .= pack('n2', 1, 1);
    return $data;
}
// function makeDnsResponse($id = 0,$flags = 0x8180,$domain = 0xC00C){
function makeDnsResponse($requestDns){
    $header = makeDnsHeader(getId($requestDns),0x8180,1);
    echo "header:",strlen($header),PHP_EOL;
    $queries = getName($requestDns);
    echo "queries:",strlen($queries),PHP_EOL;
    $answer = getAnswers();
    echo "answer:",strlen($answer),PHP_EOL;
    $response = $header . $queries . $answer;
    return $response;
}


function makeDnsHeader($id,$flags,$answer){
    return pack('n6', $id, $flags, 1, $answer, 0, 0);
}

function getAnswers(){
    $name = pack("n",0xC00C);
    $type = pack("n",0x0001);
    $class = pack("n",0x0001);
    $ttl = 0x00000108;
    $ttl = pack("L",0x00000108);
    $length = pack("n",0x0004);
    $ip = "120.27.117.52";
    $byteIp = '';
    foreach (explode(".", $ip) as $key => $value) {
        $byteIp .= pack('c',$value);
    }
    $answer = $name.$type.$class.$ttl.$length.$byteIp;
    return $answer;
}
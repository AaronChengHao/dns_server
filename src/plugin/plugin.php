<?php
include __DIR__ . '/../DnsHelper.php';

class plugin
{

    public static function begin($data,$client)
    {
        $domain = DnsHelper::getDomain($data);
        $content = "-----BEGIN-----" . PHP_EOL;
        $content .= "{$client['address']} ----> $domain". PHP_EOL;
        $content .= "-----END-----" . PHP_EOL;
        file_put_contents('./domain.log',$content,FILE_APPEND);
    }


    public static function end($response,$request,$client)
    {
        if (!$response) {
            $domain = DnsHelper::getDomain($data);
            $content = "IP:{$client['address']}   ----->域名查询失败:{$domain}" . PHP_EOL;
            $content .= " ----> $domain". PHP_EOL;
            $content .= "-----END-----" . PHP_EOL;
            file_put_contents('./domain.log',$content,FILE_APPEND);
        }
    }

}




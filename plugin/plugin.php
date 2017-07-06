<?php
include __DIR__ . '/../DnsHelper.php';

class plugin
{

    public static function handle($data,$client)
    {
        $domain = DnsHelper::getDomain($data);
        file_put_contents('./domain.log',$client['address'] . " ----> $domain ". PHP_EOL,FILE_APPEND);
    }
}




<?php



class DnsHelper
{

    public static function getId($data)
    {$domain = substr($data,12, strlen($data)-4);
            return $domain;
        return unpack("nid", $data)['id'];
    }


    public static function getDomain($data)
    {
            $domain = substr($data,12, strlen($data)-4);
            return $domain;
    }










}












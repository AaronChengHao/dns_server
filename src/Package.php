<?php


namespace Dns;




class Package
{

    protected $data = null;



    /**
     * 获取数据包ID（请求序列号）
     */
    public function getId()
    {
        return unpack("nid", $this->data)['id'];
    }

    /**
     * 获取域名
     */
    public function getDomain()
    {
        $domain = substr($this->data,12, strlen($this->data)-4);
        return $domain;
    }


    public function getFilterDomain()
    {
        $domain = substr($this->data,12, strlen($this->data)-4);

        return str_replace( ["\\n","\\t",chr(11),chr(9)], "",$domain);
    }

    public function getRawData()
    {
        return $this->data;
    }


        public function getDataLen()
    {
        return strlen($this->data);
    }
}







<?php
include "./interface/request.php";



class Request implements Request
{
    protected $data = $data;



    public function __construct($data)
    {
        $this->data = $data;
    }

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


    public function getRawData()
    {
        return $this->data;
    }


}

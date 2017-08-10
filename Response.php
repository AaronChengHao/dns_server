<?php


namespace Dns;

use Dns\Package;


class Response extends Package
{

    public function __construct($data)
    {
        $this->data = $data;
    }




    public function isEmpty()
    {
        return strlen($this->data) > 1 ? false : true;
    }


    public function getDataLen()
    {
        return strlen($this->data);
    }


}

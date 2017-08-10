<?php

namespace Dns;

use Dns\Package;



class Request extends Package
{

    public function __construct($data)
    {
        $this->data = $data;
    }


}


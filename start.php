<?php
require "vendor/autoload.php";

use Dns\Server;
use Dns\Agent;

$serverConf = ['ip' => '0.0.0.0', 'port' => 53];
$agentConf = ['ip' => '61.139.2.69', 'port' => 53];

(new Server($serverConf, new Agent($agentConf)))->listen();

























<?php
require "vendor/autoload.php";


use Dns\Server;
use Dns\Agent;

$serverConf = ['ip' => '0.0.0.0', 'port' => 53];
$agentConf = ['ip' => '192.168.5.1', 'port' => 53];

(new Server($serverConf, new Agent($agentConf)))->listen();


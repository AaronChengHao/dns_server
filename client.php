<?php
$fp = stream_socket_client("udp://192.168.5.108:53", $errno, $errstr, 30);
echo fwrite($fp, '---');
echo "string";
var_dump($fp);

echo fread($fp, 100);

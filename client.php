<?php
$fp = stream_socket_client("udp://127.0.0.1:553", $errno, $errstr, 30);
echo fwrite($fp, '11111111111111111111111111111');
echo "string";
var_dump($fp);
sleep(10);

echo fread($fp, 100);

<?php

interface Agent
{
    public function send($data);
    public function recv();
}

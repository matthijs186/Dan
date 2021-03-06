<?php

namespace Dan\Irc\Packets;

use Dan\Contracts\PacketContract;
use Dan\Irc\Connection;

class Packet003 implements PacketContract
{
    public function handle(Connection $connection, array $from, array $data)
    {
        if (!DEBUG) {
            console("[<magenta>{$from[0]}</magenta>] {$data[1]}");
        }
    }
}

<?php

namespace BitWasp\Bitcoin\Networking\Messages;

use BitWasp\Bitcoin\Networking\Message;
use BitWasp\Bitcoin\Networking\NetworkSerializable;
use BitWasp\Buffertools\Buffer;

class FilterClear extends NetworkSerializable
{
    /**
     * @return string
     */
    public function getNetworkCommand()
    {
        return Message::FILTERCLEAR;
    }

    /**
     * @return Buffer
     */
    public function getBuffer()
    {
        return new Buffer();
    }
}

<?php

namespace Selmonal\SMS\Transport;


use Log;
use Selmonal\SMS\Contracts\Message;

class LogTransport extends Transport
{
    /**
     * Send a message.
     *
     * @param Message $message
     * @return bool
     */
    function send(Message $message)
    {
        Log::info("{$message->getPhoneNumber()}: {$message->getText()}");

        $this->fireSuccessEvent($message);
    }
}
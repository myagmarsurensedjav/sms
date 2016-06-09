<?php

namespace Selmonal\SMS\Transport;


use Config;
use Guzzle\Http\Client;
use Selmonal\SMS\Contracts\Message;
use Selmonal\SMS\Detector;
use Selmonal\SMS\Events\MessageWasFailed;
use Selmonal\SMS\Events\MessageWasSent;
use Selmonal\SMS\Exceptions\TransportFailedException;

class SkytelTransport extends Transport
{
    const API_URL = 'http://web2sms.skytel.mn/apiSend';

    /**
     * @var Client
     */
    private $client;

    /**
     * SkytelTransport constructor.
     *
     * @param Detector $detector
     * @param Client $client
     */
    public function __construct(Detector $detector, Client $client)
    {
        parent::__construct($detector);

        $this->client = $client;
    }

    /**
     * Send a message.
     *
     * @param Message $message
     * @return bool
     */
    function send(Message $message)
    {
        $uri = $this->getUrl($message);

        $response = $this->client->get($uri)->send()->json();

        if($response['status'] == 0) {

            $this->fireFailedEvent($message);

            throw new TransportFailedException($response['message']);
        }

        $this->fireSuccessEvent($message);
    }

    /**
     * Get the url with parameters.
     *
     * @param Message $message
     * @return string
     */
    private function getUrl(Message $message)
    {
        return static::API_URL . "?token={$this->getToken()}&sendto={$message->getPhoneNumber()}&message={$message->getText()}";
    }

    /**
     * Get a token for the connection.
     *
     * @return string
     */
    private function getToken()
    {
        return Config::get('sms.skytel_token');
    }
}
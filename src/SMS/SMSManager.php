<?php

namespace Selmonal\SMS;

use Selmonal\SMS\Contracts\Message;
use Selmonal\SMS\Transport\Transport;


class SMSManager
{
	/**
	 * A transport instance.
	 * 
	 * @var Transport
	 */
	private $transport;

	/**
	 * A detector instance.
	 * 
	 * @var Detector
	 */
	private $detector;

	/**
	 * SMSManager Constructor.
	 * 
	 * @param Transport $transport
	 * @param Detector  $detector
	 */
	public function __construct(Transport $transport, Detector $detector)
	{
		$this->transport = $transport;
		$this->detector = $detector;
	}

	/**
	 * Send a sms.
	 *
	 * @param string $phoneNumber
	 * @param string $text
	 * @param string|null $type
	 * @return bool
	 */
	public function send($phoneNumber, $text, $type = null)
	{
		$message = $this->makeMessage($phoneNumber, $text, $this->detector->find($phoneNumber), $type);

		MessageValidator::make($message)->validate();

		$this->transport->send($message);
	}

	/**
	 * @param $phone_number
	 * @param $text
	 * @param $vendor
	 * @param $type
	 * @return mixed
	 */
	private function makeMessage($phone_number, $text, $vendor, $type)
	{
		return app(Message::class, [compact('phone_number', 'text', 'type', 'vendor')]);
	}
}
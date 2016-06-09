<?php

namespace Selmonal\SMS\Transport;

use Selmonal\SMS\Contracts\Message;
use Selmonal\SMS\Detector;
use Selmonal\SMS\Events\MessageWasFailed;
use Selmonal\SMS\Events\MessageWasSent;

abstract class Transport
{	
	/**
	 * The detector instance.
	 * 
	 * @var Detector
	 */
	protected $detector;

	/**
	 * Transport Constructor.
	 * 
	 * @param Detector $detector
	 */
	public function __construct(Detector $detector)
	{
		$this->detector = $detector;
	}

	/**
	 * Send a message.
	 *
	 * @param Message $message
	 * @return bool
	 */
	abstract function send(Message $message);

	/**
     * Get the vendor of the number.
     *
     * @param $phoneNumber
     * @return string
     */
	protected function getVendor($phoneNumber)
	{
		return $this->detector->find($phoneNumber);
	}

	/**
	 * Fire a failed event.
	 *
	 * @param Message $message
	 * @return mixed
	 */
	protected function fireFailedEvent(Message $message)
	{
		return event(new MessageWasFailed($message));
	}

	/**
	 * Fire a success event.
	 *
	 * @param Message $message
	 * @return mixed
	 */
	protected function fireSuccessEvent(Message $message)
	{
		return event(new MessageWasSent($message));
	}
}
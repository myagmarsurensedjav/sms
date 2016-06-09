<?php

namespace Selmonal\SMS\Events;

use Selmonal\SMS\Contracts\Message;

class MessageWasFailed
{
	/**
	 * @var Message
	 */
	private $message;

	/**
	 * MessageWasFailed constructor.
	 *
	 * @param Message $message
	 */
	public function __construct(Message $message)
	{
		$this->message = $message;
	}

	/**
	 * @return Message
	 */
	public function getMessage()
	{
		return $this->message;
	}
}
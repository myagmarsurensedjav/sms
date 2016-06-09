<?php

namespace Selmonal\SMS;


use Illuminate\Support\MessageBag;
use Selmonal\SMS\Contracts\Message as MessageContract;
use Selmonal\SMS\Exceptions\ValidationException;

class MessageValidator
{
    /**
     * @var MessageContract
     */
    private $message;

    /**
     * MessageValidator constructor.
     *
     * @param MessageContract $message
     */
    public function __construct(MessageContract $message)
    {
        $this->message = $message;
    }

    /**
     * Validate the message.
     *
     * @throws ValidationException
     */
    public function validate()
    {
        $errors = new MessageBag();

        if(empty($this->message->getText()) || strlen($this->message->getText()) > 160) {
            $errors->add('text', "Текст талбарт утга оруулах шаардлагатай.");
        }

        if(! preg_match('/^\d{8}$/', $this->message->getPhoneNumber())) {
            $errors->add('phone_number', "'{$this->message->getPhoneNumber()}' дугаар буруу байна.");
        }

        if($errors->count() > 0) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Make a validator.
     *
     * @param Message $message
     * @return MessageValidator
     */
    public static function make(Message $message)
    {
        return new static($message);
    }
}
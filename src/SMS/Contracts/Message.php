<?php

namespace Selmonal\SMS\Contracts;


interface Message
{
    /**
     * @return string
     */
    public function getPhoneNumber();

    /**
     * @return string
     */
    public function getText();

    /**
     * @return string
     */
    public function getType();
}
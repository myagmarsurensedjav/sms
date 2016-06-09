<?php

namespace Selmonal\SMS;

class Detector
{
    /**
     * The detector patterns.
     * 
     * @var array
     */
    private $formats = [];

    /**
     * Add a new format.
     * 
     * @param $format
     * @param $owner
     */
    public function addFormat($format, $owner)
    {
        $pattern = '/^' .str_replace('*', '\d', $format) . '$/';

        $this->formats[] = compact('format', 'owner', 'pattern');
    }

    /**
     * Find a vendor using phone number.
     *
     * @param $phoneNumber
     * @return string
     */
    public function find($phoneNumber)
    {
        foreach($this->formats as $format) {            
            if(preg_match($format['pattern'], $phoneNumber)) {
                return $format['owner'];
            }
        }
    }
}
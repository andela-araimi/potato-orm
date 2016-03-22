<?php

/**
 * class NoDataFoundException.
 *
 * @extends Exception
 *
 * Throws an error if the index for
 * the key is not found in the
 * array
 *
 * @author: Raimi Ademola <ademola.raimi@andela.com>
 * @copyright: 2016 Andela
 */
namespace Demo;

use Exception;

class IdNotFoundException extends Exception
{
    public $message;

    /**
     * @param string $errorMessage The message from the Exception
     */
    public function __construct($errorMessage)
    {
        return $this->message = $errorMessage;
    }
}

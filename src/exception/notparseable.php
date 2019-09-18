<?php

namespace Exception;

use InvalidArgumentException;

/**
 * Meaning that the given NPM is malformed, contains bad charater, or invalid jurusan ID.
 */
class NotParseable extends InvalidArgumentException
{
    public function __construct($message = 'The given NPM is malformed, contains bad charater, or invalid jurusan ID', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

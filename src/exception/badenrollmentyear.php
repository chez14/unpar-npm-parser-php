<?php

namespace Exception;

use InvalidArgumentException;

/**
 * Meaning that the given NPM has out-of-range of the current solvers.
 */
class BadEnrollmentYear extends InvalidArgumentException
{
    public function __construct($message = 'Enrollment year is out-of-range', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

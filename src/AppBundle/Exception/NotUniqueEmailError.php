<?php

namespace AppBundle\Exception;

use Exception;

class NotUniqueEmailError extends AppException
{
    public function __construct($message = '', $code = 400, Exception $previous = null)
    {
        parent::__construct('E-mail address already exists.', $code, $previous);
    }
}

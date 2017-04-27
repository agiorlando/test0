<?php

namespace AppBundle\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ValidationError extends AppException
{
    public function __construct($message = '', $code = Response::HTTP_BAD_REQUEST, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

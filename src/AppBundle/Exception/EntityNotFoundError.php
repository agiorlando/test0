<?php

namespace AppBundle\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class EntityNotFoundError extends AppException
{
    public function __construct($message = '', $code = Response::HTTP_NOT_FOUND, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

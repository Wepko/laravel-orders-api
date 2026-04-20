<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidOrderDataException extends Exception
{
    protected $message = 'Invalid order data provided.';
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
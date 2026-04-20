<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CustomerNotFoundException extends Exception
{
    protected $message = 'Customer not found.';
    protected $code = Response::HTTP_NOT_FOUND;
}
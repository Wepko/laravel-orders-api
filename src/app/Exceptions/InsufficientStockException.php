<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InsufficientStockException extends Exception
{
    protected $message = 'Insufficient stock for the requested product.';
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
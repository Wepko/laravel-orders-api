<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class OrderStatusUpdateException extends RuntimeException
{
    protected $message = 'Invalid order status transition.';
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ProductNotFoundException extends Exception
{
    protected $message = 'Product not found.';
    protected $code = Response::HTTP_NOT_FOUND;
}
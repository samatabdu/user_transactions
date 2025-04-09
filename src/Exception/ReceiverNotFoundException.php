<?php

declare(strict_types=1);

namespace App\Exception;

class ReceiverNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('receiver not found');
    }
}
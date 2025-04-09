<?php

declare(strict_types=1);

namespace App\Exception;

class SenderNotEnouphBalanceException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('sender not enouph balance');
    }
}
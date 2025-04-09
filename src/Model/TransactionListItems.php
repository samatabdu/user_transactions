<?php

namespace App\Model;

class TransactionListItems
{
    public function __construct(private readonly int $fromUserId, private readonly int $toUserId, private readonly string $amount)
    {
    }

    /**
     * @return int
     */
    public function getFromUserId(): int
    {
        return $this->fromUserId;
    }

    /**
     * @return int
     */
    public function getToUserId(): int
    {
        return $this->toUserId;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }
}
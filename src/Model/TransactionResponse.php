<?php

namespace App\Model;

class TransactionResponse
{
    /**
     * @var TransactionListItems[]
     */
    private array $items;

    /**
     * @param TransactionListItems[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return TransactionListItems[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
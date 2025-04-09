<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TransferRequest
{
    #[Assert\NotBlank]
    private int $fromUserId;

    #[Assert\NotBlank]
    private int $toUserId;

    #[Assert\NotBlank]
    private readonly float $amout;

    /**
     * @return int
     */
    public function getFromUserId(): int
    {
        return $this->fromUserId;
    }

    /**
     * @param int $fromUserId
     */
    public function setFromUserId(int $fromUserId): self
    {
        $this->fromUserId = $fromUserId;

        return $this;
    }

    /**
     * @return int
     */
    public function getToUserId(): int
    {
        return $this->toUserId;
    }

    /**
     * @param int $toUserId
     */
    public function setToUserId(int $toUserId): self
    {
        $this->toUserId = $toUserId;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amout;
    }

    public function setAmount(float $amout): self
    {
        $this->amout = $amout;

        return $this;
    }
}
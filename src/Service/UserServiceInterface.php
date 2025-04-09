<?php

namespace App\Service;

use App\Model\CreateUserRequest;
use App\Model\IdResponse;
use App\Model\TransactionResponse;
use App\Model\TransferRequest;
use App\Model\UserDepositeRequest;

interface UserServiceInterface
{
    public function getTransactionsByUser(int $userId): TransactionResponse;

    public function updateUserBalance(int $userId, UserDepositeRequest $request): IdResponse;

    public function transferBalance(TransferRequest $request): IdResponse;

    public function createUser(CreateUserRequest $request): IdResponse;
}
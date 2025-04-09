<?php

namespace App\Service;

use App\Entity\Transactions;
use App\Entity\Users;
use App\Exception\IncorrectBalanceException;
use App\Exception\ReceiverNotFoundException;
use App\Exception\SenderNotEnouphBalanceException;
use App\Exception\UserAlreadyExistsException;
use App\Exception\UserNotFoundException;
use App\Model\CreateUserRequest;
use App\Model\IdResponse;
use App\Model\TransactionListItems;
use App\Model\TransactionResponse;
use App\Model\TransferRequest;
use App\Model\UserDepositeRequest;
use App\Repository\TransactionsRepository;
use App\Repository\UsersRepository;

class UserService implements UserServiceInterface
{
    public function __construct(
        private TransactionsRepository $transactionRepository,
        private UsersRepository $userRepository,
    ) { }

    public function getTransactionsByUser(int $userId): TransactionResponse
    {
        $user = $this->userRepository->getUser($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $transactions = $this->transactionRepository->findByFromUser($userId);

        return new TransactionResponse(array_map(
            fn(Transactions $transaction) => new TransactionListItems(
                $transaction->getFromUserId(),
                $transaction->getToUserId(),
                $transaction->getAmount()
            ),
            $transactions
        ));
    }

    public function updateUserBalance(int $userId, UserDepositeRequest $request): IdResponse
    {
        $amount = $request->getAmount();
        $user = $this->userRepository->getUser($userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        if (!$amount) {
            throw new IncorrectBalanceException();
        }

        $this->userRepository->getEntityManager()->beginTransaction();
        try {
            $newBalance = $user->getBalance() + $amount;
            $user->setBalance($newBalance);
            $this->userRepository->saveAndCommit($user);

            $transaction = $this->createTransactions($userId, $userId, $amount, 'deposite');
            $this->transactionRepository->saveAndCommit($transaction);

            $this->userRepository->getEntityManager()->commit();
        } catch (\Exception $e) {
            $this->userRepository->getEntityManager()->rollback();

            throw $e;
        }

        return new IdResponse($user->getId());
    }

    public function transferBalance(TransferRequest $request): IdResponse
    {
        $amount = $request->getAmount();
        $sender = $this->userRepository->getUser($request->getFromUserId());
        if (!$sender) {
            throw new UserNotFoundException();
        }

        if (!$amount) {
            throw new IncorrectBalanceException();
        }

        if ($sender->getBalance() < $amount) {
            throw new SenderNotEnouphBalanceException();
        }

        $receiver = $this->userRepository->getUser($request->getToUserId());
        if (!$receiver) {
            throw new ReceiverNotFoundException();
        }

        if ($sender->getId() === $receiver->getId()) {
            throw new ReceiverNotFoundException();
        }

        $this->userRepository->getEntityManager()->beginTransaction();
        try {
            $sender->setBalance($sender->getBalance() - $amount);
            $this->userRepository->save($sender);

            $receiver->setBalance($receiver->getBalance() + $amount);
            $this->userRepository->save($receiver);

            $transaction = $this->createTransactions($sender->getId(), $receiver->getId(), $amount, 'transfer');
            $this->transactionRepository->saveAndCommit($transaction);

            $this->userRepository->getEntityManager()->commit();
        } catch (\Exception $e) {
            $this->userRepository->getEntityManager()->rollback();

            throw $e;
        }

        return new IdResponse($receiver->getId());
    }

    public function createUser(CreateUserRequest $createUserRequest): IdResponse
    {
        $user = $this->userRepository->existsByEmail($createUserRequest->getEmail());
        if ($user) {
            throw new UserAlreadyExistsException();
        }

        $user = new Users();
        $user->setName($createUserRequest->getName());
        $user->setEmail($createUserRequest->getEmail());

        $this->userRepository->saveAndCommit($user);

        return new IdResponse($user->getId());
    }

    public function createTransactions(int $fromUserId, int $toUserId, float $amount, string $type): Transactions
    {
        $transaction = new Transactions();
        $transaction->setFromUserId($fromUserId);
        $transaction->setToUserId($toUserId);
        $transaction->setAmount($amount);
        $transaction->setType($type);
        $transaction->setCreatedAt(new \DateTimeImmutable());

        return $transaction;
    }
}
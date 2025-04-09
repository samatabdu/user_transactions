<?php

namespace App\Tests\Service;

use App\Entity\Transactions;
use App\Entity\Users;
use App\Model\CreateUserRequest;
use App\Model\IdResponse;
use App\Model\TransactionListItems;
use App\Model\TransactionResponse;
use App\Repository\TransactionsRepository;
use App\Repository\UsersRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TransactionServiceTest extends TestCase
{
    private TransactionsRepository $transactionRepository;

    private UsersRepository $userRepository;

    private EntityManagerInterface $entityManager;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionRepository = $this->createMock(TransactionsRepository::class);
        $this->userRepository = $this->createMock(UsersRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userService = new UserService($this->transactionRepository, $this->userRepository, $this->entityManager);
    }

    public function testUserServiceGetTransaction(): void
    {
        $this->userRepository->expects($this->once())
            ->method('getUser')
            ->willReturn((new Users())->setName('Asan')->setEmail('test@mail.ru'));

        $this->transactionRepository->expects($this->once())
            ->method('findByFromUser')
            ->willReturn([(new Transactions())->setFromUserId(1)->setToUserId(2)->setAmount(200)]);

        $expected = new TransactionResponse([new TransactionListItems(1, 2, 200)]);

        $this->assertEquals($expected, $this->userService->getTransactionsByUser(1));
    }

    public function testCreateUsers(): void
    {
        $payload = new CreateUserRequest();
        $payload->setName('Asan');
        $payload->setEmail('asan@mail.ru');

        $expectedUser = (new Users())
            ->setName('Asan')
            ->setEmail('asan@mail.ru');

        $this->userRepository->expects($this->once())
            ->method('existsByEmail')
            ->with('asan@mail.ru')
            ->willReturn(false);

        $this->userRepository->expects($this->once())
            ->method('saveAndCommit')
            ->with($expectedUser)
            ->will($this->returnCallback(function (Users $user) {
                $this->setEntityId($user, 3);
            }));

        $this->assertEquals(new IdResponse(3), $this->userService->createUser($payload));
    }

    private function setEntityId(object $entity, int $value, $idField = 'id')
    {
        $class = new \ReflectionClass($entity);
        $property = $class->getProperty($idField);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        $property->setAccessible(false);
    }
}

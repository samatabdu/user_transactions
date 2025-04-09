<?php

namespace App\Controller;

use App\Model\CreateUserRequest;
use App\Model\ErrorResponse;
use App\Model\IdResponse;
use App\Model\TransactionResponse;
use App\Model\TransferRequest;
use App\Model\UserDepositeRequest;
use App\Service\UserServiceInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use App\Attribute\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class UsersController extends AbstractController
{
    public function __construct(private readonly UserServiceInterface $userService)
    {
    }
//
//    #[Route('/api/products', methods: ['GET'])]
//    #[OA\Get(
//        path: '/api/products',
//        summary: 'Список продуктов',
//        responses: [
//            new OA\Response(response: 200, description: 'OK')
//        ]
//    )]
//    public function list(): Response
//    {
//        return $this->json(['product1', 'product2']);
//    }

    #[Route('/users/{id}/transactions', name: 'users_transactions', methods: ['GET'], format: 'json')]
    #[OA\Tag(name: 'Users API')]
    #[OA\Response(response: 200, description: 'Returns user transactions', attachables: [new Model(type: TransactionResponse::class)])]
    #[OA\Response(response: 404, description: 'User transactions not found', attachables: [new Model(type: ErrorResponse::class)])]
    public function usersTransactions(int $id): Response
    {
        return $this->json($this->userService->getTransactionsByUser($id));
    }

    #[Route('/users',  name: 'customer_create', methods: ['POST'], format: 'json')]
    #[OA\Tag(name: 'Users API')]
    #[OA\Response(response: 200, description: 'Create a user', attachables: [new Model(type: IdResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: CreateUserRequest::class)])]
    public function create(#[RequestBody] CreateUserRequest $request): Response
    {
        return $this->json($this->userService->createUser($request));
    }

    #[Route('/users/{id}/deposit',  name: 'deposit_balance', methods: ['POST'], format: 'json')]
    #[OA\Tag(name: 'Users API')]
    #[OA\Response(response: 200, description: 'update a balance', attachables: [new Model(type: IdResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: UserDepositeRequest::class)])]
    public function deposit(int $id, #[RequestBody] UserDepositeRequest $request): Response
    {
        return $this->json($this->userService->updateUserBalance($id, $request));
    }

    #[Route('/transactions/transfer',  name: 'transfer_balance', methods: ['POST'], format: 'json')]
    #[OA\Tag(name: 'Users API')]
    #[OA\Response(response: 200, description: 'transfer balance', attachables: [new Model(type: IdResponse::class)])]
    #[OA\Response(response: 400, description: 'Validation failed', attachables: [new Model(type: ErrorResponse::class)])]
    #[OA\RequestBody(attachables: [new Model(type: TransferRequest::class)])]
    public function transfer(#[RequestBody] TransferRequest $request): Response
    {
        return $this->json($this->userService->transferBalance($request));
    }
}
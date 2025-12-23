<?php

namespace App\Domain\Transaction\Controller;

use App\Domain\Transaction\Service\TransactionService;
use App\Domain\Auth\Service\AuthService;
use App\Shared\Service\ErrorCodeResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\Domain\Transaction\DTO\CreateTransactionDTO;
use App\Domain\Transaction\DTO\ExchangeOfficeIDQueryDTO;
use App\Domain\Transaction\DTO\ClientIDQueryDTO;
use App\Domain\Transaction\DTO\TransactionIDQueryDTO;
use App\Domain\Transaction\DTO\UpdateTransactionDTO;
use OpenApi\Attributes as OA;

#[Route('/api/transactions', name: 'api_transactions_')]
class TransactionController extends AbstractController
{
    private TransactionService $transactionService;
    private AuthService $authService;
    private ErrorCodeResolver $errorCodeResolver;
    private ValidatorInterface $validator;

    public function __construct(
        TransactionService $transactionService,
        AuthService $authService,
        ErrorCodeResolver $errorCodeResolver,
        ValidatorInterface $validator
    ) {
        $this->transactionService = $transactionService;
        $this->authService = $authService;
        $this->errorCodeResolver = $errorCodeResolver;
        $this->validator = $validator;
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/transactions',
        summary: 'Create a new transaction (Agent only)',
        description: 'Create a new transaction for a client of the agent\'s exchange office. Only agents can create transactions for their office clients.',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            description: 'Transaction data to create',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    'clientId' => new OA\Property(
                        property: 'clientId', 
                        type: 'string', 
                        format: 'uuid', 
                        description: 'UUID of the client for this transaction', 
                        example: '550e8400-e29b-41d4-a716-446655440000'
                    ),
                    'amount' => new OA\Property(
                        property: 'amount', 
                        type: 'string', 
                        pattern: '^\d+(\.\d{1,2})?$',
                        description: 'Transaction amount with up to 2 decimal places', 
                        example: '1500.50'
                    ),
                    'sourceCurrency' => new OA\Property(
                        property: 'sourceCurrency', 
                        type: 'string', 
                        enum: ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'CNY', 'MAD', 'DZD', 'TND', 'EGP', 'SAR', 'AED', 'QAR', 'KWD'],
                        description: 'Source currency code', 
                        example: 'EUR'
                    ),
                    'targetCurrency' => new OA\Property(
                        property: 'targetCurrency', 
                        type: 'string', 
                        enum: ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'CNY', 'MAD', 'DZD', 'TND', 'EGP', 'SAR', 'AED', 'QAR', 'KWD'],
                        description: 'Target currency code', 
                        example: 'MAD'
                    ),
                    'exchangeRate' => new OA\Property(
                        property: 'exchangeRate', 
                        type: 'string', 
                        pattern: '^\d+(\.\d{1,6})?$',
                        description: 'Exchange rate with up to 6 decimal places', 
                        example: '10.850000'
                    ),
                    'transactionDate' => new OA\Property(
                        property: 'transactionDate', 
                        type: 'string', 
                        format: 'date',
                        description: 'Transaction date in YYYY-MM-DD format', 
                        example: '2025-01-25'
                    )
                ],
                required: ['clientId', 'amount', 'sourceCurrency', 'targetCurrency', 'exchangeRate', 'transactionDate']
            )
        ),
        tags: ['Transactions'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Transaction created successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Transaction created successfully'),
                        'transaction' => new OA\Property(
                            property: 'transaction',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'amount' => new OA\Property(property: 'amount', type: 'string'),
                                'sourceCurrency' => new OA\Property(property: 'sourceCurrency', type: 'string'),
                                'targetCurrency' => new OA\Property(property: 'targetCurrency', type: 'string'),
                                'exchangeRate' => new OA\Property(property: 'exchangeRate', type: 'string'),
                                'transactionDate' => new OA\Property(property: 'transactionDate', type: 'string', format: 'datetime'),
                                'client' => new OA\Property(
                                    property: 'client',
                                    type: 'object',
                                    properties: [
                                        'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                        'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                        'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                        'email' => new OA\Property(property: 'email', type: 'string', format: 'email')
                                    ]
                                ),
                                'exchangeOffice' => new OA\Property(
                                    property: 'exchangeOffice',
                                    type: 'object',
                                    properties: [
                                        'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                        'name' => new OA\Property(property: 'name', type: 'string')
                                    ]
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request - Invalid data'),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - Not an agent or client not in agent\'s office'),
            new OA\Response(response: 404, description: 'Not found - Client not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function create(#[MapRequestPayload] CreateTransactionDTO $dto ,Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);
            
            $transactionResponseDTO = $this->transactionService->createTransactionFromDto(
                $dto,
                $currentUser
            );

            return new JsonResponse(
                $transactionResponseDTO->toArray(),
                Response::HTTP_CREATED
            );

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->errorCodeResolver->getStatusCode($e->getMessage());
            
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $statusCode);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/my-office', name: 'list_for_agent', methods: ['GET'])]
    #[OA\Get(
        path: '/api/transactions/my-office',
        summary: 'List transactions for agent\'s exchange office',
        description: 'Get all transactions for the exchange office where the agent works. Only agents can access this endpoint.',
        security: [['Bearer' => []]],
        tags: ['Transactions'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Transactions retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Transactions retrieved successfully'),
                        'transactions' => new OA\Property(
                            property: 'transactions',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    'amount' => new OA\Property(property: 'amount', type: 'string'),
                                    'sourceCurrency' => new OA\Property(property: 'sourceCurrency', type: 'string'),
                                    'targetCurrency' => new OA\Property(property: 'targetCurrency', type: 'string'),
                                    'exchangeRate' => new OA\Property(property: 'exchangeRate', type: 'string'),
                                    'transactionDate' => new OA\Property(property: 'transactionDate', type: 'string', format: 'datetime'),
                                    'client' => new OA\Property(
                                        property: 'client',
                                        type: 'object',
                                        properties: [
                                            'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                            'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                            'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                            'email' => new OA\Property(property: 'email', type: 'string', format: 'email')
                                        ]
                                    )
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - Not an agent'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function listForAgent(Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);

            $transactionListResponseDTO = $this->transactionService->getTransactionsForAgent($currentUser);

            return new JsonResponse(
                $transactionListResponseDTO->toArray(),
                Response::HTTP_OK
            );

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->errorCodeResolver->getStatusCode($e->getMessage());
            
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $statusCode);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/by-exchange-office', name: 'list_by_exchange_office', methods: ['GET'])]
    #[OA\Get(
        path: '/api/transactions/by-exchange-office',
        summary: 'List transactions by exchange office (Admin only)',
        description: 'Get all transactions for a specific exchange office by its ID. Only administrators can access this endpoint.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'exchangeOfficeId',
                description: 'Exchange office UUID',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        tags: ['Transactions'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Transactions retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Transactions retrieved successfully'),
                        'exchangeOffice' => new OA\Property(
                            property: 'exchangeOffice',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'name' => new OA\Property(property: 'name', type: 'string', example: 'Bureau Central Paris')
                            ]
                        ),
                        'transactions' => new OA\Property(
                            property: 'transactions',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    'amount' => new OA\Property(property: 'amount', type: 'string'),
                                    'sourceCurrency' => new OA\Property(property: 'sourceCurrency', type: 'string'),
                                    'targetCurrency' => new OA\Property(property: 'targetCurrency', type: 'string'),
                                    'exchangeRate' => new OA\Property(property: 'exchangeRate', type: 'string'),
                                    'transactionDate' => new OA\Property(property: 'transactionDate', type: 'string', format: 'datetime'),
                                    'client' => new OA\Property(
                                        property: 'client',
                                        type: 'object',
                                        properties: [
                                            'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                            'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                            'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                            'email' => new OA\Property(property: 'email', type: 'string', format: 'email')
                                        ]
                                    )
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request - Exchange office ID is required'),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - Not an admin'),
            new OA\Response(response: 404, description: 'Not found - Exchange office not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function listByExchangeOffice(#[MapQueryString] ExchangeOfficeIDQueryDTO $queryDto): JsonResponse
    {
        try {
            $exchangeOfficeId = $queryDto->exchangeOfficeId;

            $transactionListResponseDTO = $this->transactionService->getTransactionsByExchangeOffice($exchangeOfficeId);

            return new JsonResponse(
                $transactionListResponseDTO->toArray(),
                Response::HTTP_OK
            );

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->errorCodeResolver->getStatusCode($e->getMessage());
            
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $statusCode);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/by-client', name: 'list_by_client', methods: ['GET'])]
    #[OA\Get(
        path: '/api/transactions/by-client',
        summary: 'List transactions by client (Agent and Admin access)',
        description: 'Get all transactions for a specific client by their ID. Agents can only view transactions for clients of their exchange office. Administrators can view transactions for any client.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'clientId',
                description: 'Client UUID',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        tags: ['Transactions'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Client transactions retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Client transactions retrieved successfully'),
                        'transactions' => new OA\Property(
                            property: 'transactions',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    'amount' => new OA\Property(property: 'amount', type: 'string'),
                                    'sourceCurrency' => new OA\Property(property: 'sourceCurrency', type: 'string'),
                                    'targetCurrency' => new OA\Property(property: 'targetCurrency', type: 'string'),
                                    'exchangeRate' => new OA\Property(property: 'exchangeRate', type: 'string'),
                                    'transactionDate' => new OA\Property(property: 'transactionDate', type: 'string', format: 'datetime'),
                                    'client' => new OA\Property(
                                        property: 'client',
                                        type: 'object',
                                        properties: [
                                            'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                            'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                            'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                            'email' => new OA\Property(property: 'email', type: 'string', format: 'email')
                                        ]
                                    ),
                                    'exchangeOffice' => new OA\Property(
                                        property: 'exchangeOffice',
                                        type: 'object',
                                        properties: [
                                            'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                            'name' => new OA\Property(property: 'name', type: 'string')
                                        ]
                                    )
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request - Client ID is required'),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - Not an agent/admin or client not in agent\'s office'),
            new OA\Response(response: 404, description: 'Not found - Client not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function listByClient(#[MapQueryString] ClientIDQueryDTO $queryDto, Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);

            // Get clientId from query parameters
            $clientId = $queryDto->clientId;
   
            $transactionListResponseDTO = $this->transactionService->getTransactionsByClient($currentUser, $clientId);

            return new JsonResponse(
                $transactionListResponseDTO->toArray(),
                Response::HTTP_OK
            );

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->errorCodeResolver->getStatusCode($e->getMessage());
            
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $statusCode);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/update', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/transactions/update',
        summary: 'Update a transaction (Admin only)',
        description: 'Update an existing transaction by its ID. Only administrators can update transactions.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'transactionId',
                description: 'Transaction UUID',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        requestBody: new OA\RequestBody(
            description: 'Transaction data to update',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    'amount' => new OA\Property(
                        property: 'amount', 
                        type: 'string', 
                        pattern: '^\d+(\.\d{1,2})?$',
                        description: 'Transaction amount with up to 2 decimal places', 
                        example: '1500.50'
                    ),
                    'sourceCurrency' => new OA\Property(
                        property: 'sourceCurrency', 
                        type: 'string', 
                        enum: ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'CNY', 'MAD', 'DZD', 'TND', 'EGP', 'SAR', 'AED', 'QAR', 'KWD'],
                        description: 'Source currency code', 
                        example: 'EUR'
                    ),
                    'targetCurrency' => new OA\Property(
                        property: 'targetCurrency', 
                        type: 'string', 
                        enum: ['USD', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'CNY', 'MAD', 'DZD', 'TND', 'EGP', 'SAR', 'AED', 'QAR', 'KWD'],
                        description: 'Target currency code', 
                        example: 'MAD'
                    ),
                    'exchangeRate' => new OA\Property(
                        property: 'exchangeRate', 
                        type: 'string', 
                        pattern: '^\d+(\.\d{1,6})?$',
                        description: 'Exchange rate with up to 6 decimal places', 
                        example: '10.850000'
                    ),
                    'transactionDate' => new OA\Property(
                        property: 'transactionDate', 
                        type: 'string', 
                        format: 'date',
                        description: 'Transaction date in YYYY-MM-DD format', 
                        example: '2025-01-25'
                    )
                ],
                required: ['amount', 'sourceCurrency', 'targetCurrency', 'exchangeRate', 'transactionDate']
            )
        ),
        tags: ['Transactions'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Transaction updated successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Transaction created successfully'),
                        'transaction' => new OA\Property(
                            property: 'transaction',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'amount' => new OA\Property(property: 'amount', type: 'string'),
                                'sourceCurrency' => new OA\Property(property: 'sourceCurrency', type: 'string'),
                                'targetCurrency' => new OA\Property(property: 'targetCurrency', type: 'string'),
                                'exchangeRate' => new OA\Property(property: 'exchangeRate', type: 'string'),
                                'transactionDate' => new OA\Property(property: 'transactionDate', type: 'string', format: 'datetime'),
                                'client' => new OA\Property(
                                    property: 'client',
                                    type: 'object',
                                    properties: [
                                        'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                        'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                        'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                        'email' => new OA\Property(property: 'email', type: 'string', format: 'email')
                                    ]
                                ),
                                'exchangeOffice' => new OA\Property(
                                    property: 'exchangeOffice',
                                    type: 'object',
                                    properties: [
                                        'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                        'name' => new OA\Property(property: 'name', type: 'string')
                                    ]
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request - Invalid data or Transaction ID is required'),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - Not an admin'),
            new OA\Response(response: 404, description: 'Not found - Transaction not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function update(#[MapQueryString] TransactionIDQueryDTO $queryDto, 
    #[MapRequestPayload] UpdateTransactionDTO $dto): JsonResponse
    {
        try {

            $transactionId = $queryDto->transactionId;

            $transactionResponseDTO = $this->transactionService->updateTransaction($transactionId, $dto);

            return new JsonResponse(
                $transactionResponseDTO->toArray(),
                Response::HTTP_OK
            );

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->errorCodeResolver->getStatusCode($e->getMessage());
            
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $statusCode);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/transactions/delete',
        summary: 'Delete a transaction (Admin only)',
        description: 'Delete an existing transaction by its ID. Only administrators can delete transactions.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'transactionId',
                description: 'Transaction UUID',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            )
        ],
        tags: ['Transactions'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Transaction deleted successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Transaction deleted successfully'),
                        'transactionId' => new OA\Property(property: 'transactionId', type: 'string', format: 'uuid')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request - Transaction ID is required'),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - Not an admin'),
            new OA\Response(response: 404, description: 'Not found - Transaction not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function delete(#[MapQueryString] TransactionIDQueryDTO $queryDto): JsonResponse
    {
        try {            
            $transactionId = $queryDto->transactionId;

            $transactionDeleteResponseDTO = $this->transactionService->deleteTransaction($transactionId);

            return new JsonResponse(
                $transactionDeleteResponseDTO->toArray(),
                Response::HTTP_OK
            );

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->errorCodeResolver->getStatusCode($e->getMessage());
            
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $statusCode);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

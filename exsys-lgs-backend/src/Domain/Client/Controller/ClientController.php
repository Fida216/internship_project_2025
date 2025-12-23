<?php

namespace App\Domain\Client\Controller;

use App\Domain\Client\Service\ClientService;
use App\Domain\ExchangeOffice\Service\ExchangeOfficeService;
use App\Domain\Auth\Service\AuthService;
use App\Shared\Service\ErrorCodeResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\Domain\Client\DTO\CreateClientDTO;
use App\Domain\Client\DTO\ClientIDQueryDTO;
use App\Domain\Client\DTO\UpdateClientDTO;
use App\Domain\Client\DTO\ClientsFilterDTO;
use App\Domain\Client\DTO\AdminClientsFilterDTO;
use OpenApi\Attributes as OA;

#[Route('/api/clients', name: 'api_clients_')]
class ClientController extends AbstractController
{
    private ClientService $clientService;
    private ExchangeOfficeService $exchangeOfficeService;
    private AuthService $authService;
    private ErrorCodeResolver $errorCodeResolver;

    public function __construct(
        ClientService $clientService, 
        ExchangeOfficeService $exchangeOfficeService, 
        AuthService $authService,
        ErrorCodeResolver $errorCodeResolver
    ) {
        $this->clientService = $clientService;
        $this->exchangeOfficeService = $exchangeOfficeService;
        $this->authService = $authService;
        $this->errorCodeResolver = $errorCodeResolver;
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/clients',
        summary: 'Create a new client',
        description: 'Create a new client for the agent\'s exchange office. Only agents can create clients.',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            description: 'Client data to create',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    'lastName' => new OA\Property(property: 'lastName', type: 'string', minLength: 2, maxLength: 100, description: 'Client last name', example: 'Dupont'),
                    'firstName' => new OA\Property(property: 'firstName', type: 'string', minLength: 2, maxLength: 100, description: 'Client first name', example: 'Jean'),
                    'birthDate' => new OA\Property(property: 'birthDate', type: 'string', format: 'date', description: 'Client birth date in YYYY-MM-DD format', example: '1990-01-15'),
                    'email' => new OA\Property(property: 'email', type: 'string', format: 'email', description: 'Client email address', example: 'jean.dupont@email.com'),
                    'phone' => new OA\Property(property: 'phone', type: 'string', pattern: '^\+?[0-9\s\-\(\)]{8,20}$', description: 'Client phone number', example: '+33123456789'),
                    'whatsapp' => new OA\Property(property: 'whatsapp', type: 'string', pattern: '^\+?[0-9\s\-\(\)]{8,20}$', description: 'Client WhatsApp number (optional)', example: '+33123456789', nullable: true),
                    'nationalId' => new OA\Property(property: 'nationalId', type: 'string', description: 'National ID number (optional if passport provided)', example: '1234567890123', nullable: true),
                    'passport' => new OA\Property(property: 'passport', type: 'string', description: 'Passport number (optional if nationalId provided)', example: 'AB1234567', nullable: true),
                    'nationality' => new OA\Property(property: 'nationality', type: 'string', description: 'Client nationality', example: 'French'),
                    'residence' => new OA\Property(property: 'residence', type: 'string', description: 'Client residence address', example: '123 Rue de la Paix, Paris'),
                    'gender' => new OA\Property(property: 'gender', type: 'string', enum: ['male', 'female'], description: 'Client gender', example: 'male'),
                    'acquisitionSource' => new OA\Property(property: 'acquisitionSource', type: 'string', enum: ['online', 'walk_in', 'referral', 'phone_call', 'email', 'social_media', 'advertising', 'partnership', 'agent_direct', 'other'], description: 'How the client was acquired', example: 'walk_in'),
                    'currentSegment' => new OA\Property(property: 'currentSegment', type: 'string', description: 'Current client segment (optional)', example: 'VIP', nullable: true)                ],
                required: ['lastName', 'firstName', 'birthDate', 'email', 'phone', 'nationality', 'residence', 'gender', 'acquisitionSource', 'exchangeOfficeId']
            )
        ),
        tags: ['Clients'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Client created successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Client created successfully'),
                        'client' => new OA\Property(
                            property: 'client',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                'phone' => new OA\Property(property: 'phone', type: 'string'),
                                'nationality' => new OA\Property(property: 'nationality', type: 'string'),
                                'status' => new OA\Property(property: 'status', type: 'string'),
                                'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 409, description: 'Conflict'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function create(#[MapRequestPayload] CreateClientDTO $dto , Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);

            $responseDto = $this->clientService->createClientFromDTO($dto, $currentUser);


            return new JsonResponse([
                'message' => 'Client created successfully',
                'client' => $responseDto->toArray()
            ], Response::HTTP_CREATED);

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

    #[Route('/by-office', name: 'list_by_office', methods: ['GET'])]
    #[OA\Get(
        path: '/api/clients/by-office',
        summary: 'List clients by exchange office (Admin only)',
        description: 'Get paginated list of clients from a specific exchange office. Only administrators can access this endpoint and must specify the exchange office ID.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, default: 1, example: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Number of items per page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 20, example: 20)
            ),
            new OA\Parameter(
                name: 'search',
                description: 'Search term for filtering clients by name, email, or phone',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'dupont')
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Filter by client status',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['active', 'inactive'], example: 'active')
            ),
            new OA\Parameter(
                name: 'nationality',
                description: 'Filter by nationality',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'french')
            ),
            new OA\Parameter(
                name: 'gender',
                description: 'Filter by gender',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['male', 'female'], example: 'male')
            ),
            new OA\Parameter(
                name: 'exchangeOfficeId',
                description: 'Filter by exchange office UUID (required)',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')
            ),
            new OA\Parameter(
                name: 'acquisitionSource',
                description: 'Filter by acquisition source',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['online','walk_in','referral','phone_call','email','social_media','advertising','partnership','agent_direct','other'],
                    example: 'walk_in'
                )
            ),
            new OA\Parameter(
                name: 'currentSegment',
                description: 'Filter by current client segment',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'VIP')
            )
        ],
        tags: ['Clients'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of clients retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'clients' => new OA\Property(
                            property: 'clients',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                    'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                    'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                    'phone' => new OA\Property(property: 'phone', type: 'string'),
                                    'nationality' => new OA\Property(property: 'nationality', type: 'string'),
                                    'status' => new OA\Property(property: 'status', type: 'string'),
                                    'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime')
                                ]
                            )
                        ),
                        'totalClients' => new OA\Property(property: 'totalClients', type: 'integer', example: 150),
                        'totalPages' => new OA\Property(property: 'totalPages', type: 'integer', example: 8),
                        'currentPage' => new OA\Property(property: 'currentPage', type: 'integer', example: 1),
                        'hasNextPage' => new OA\Property(property: 'hasNextPage', type: 'boolean', example: true),
                        'hasPreviousPage' => new OA\Property(property: 'hasPreviousPage', type: 'boolean', example: false)
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function list(#[MapQueryString] AdminClientsFilterDTO $filterDto): JsonResponse
    {
        try {

            $listResponseDto = $this->clientService->getAllClientsForAdmin($filterDto);
            
            return new JsonResponse($listResponseDto->toArray());

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/my-clients', name: 'my_clients', methods: ['GET'])]
    #[OA\Get(
        path: '/api/clients/my-clients',
        summary: 'Get my office clients',
        description: 'Get list of clients from the current agent\'s exchange office. Only agents can access this endpoint.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, default: 1, example: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Number of items per page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 20, example: 20)
            ),
            new OA\Parameter(
                name: 'search',
                description: 'Search term for filtering clients by name, email, or phone',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'dupont')
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Filter by client status',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['active', 'inactive'], example: 'active')
            ),
            new OA\Parameter(
                name: 'nationality',
                description: 'Filter by nationality',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'french')
            ),
            new OA\Parameter(
                name: 'gender',
                description: 'Filter by gender',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['male', 'female'], example: 'male')
                ),
            new OA\Parameter(
                name: 'acquisitionSource',
                description: 'Filter by acquisition source',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['online','walk_in','referral','phone_call','email','social_media','advertising','partnership','agent_direct','other'],
                    example: 'walk_in'
                )
            ),
            new OA\Parameter(
                name: 'currentSegment',
                description: 'Filter by current client segment',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'VIP')
            )
        ],
        tags: ['Clients'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'My office clients retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'clients' => new OA\Property(
                            property: 'clients',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                    'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                    'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                    'phone' => new OA\Property(property: 'phone', type: 'string'),
                                    'nationality' => new OA\Property(property: 'nationality', type: 'string'),
                                    'status' => new OA\Property(property: 'status', type: 'string'),
                                    'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime')
                                ]
                            )
                        ),
                        'totalClients' => new OA\Property(property: 'totalClients', type: 'integer', example: 25),
                        'totalPages' => new OA\Property(property: 'totalPages', type: 'integer', example: 2),
                        'currentPage' => new OA\Property(property: 'currentPage', type: 'integer', example: 1),
                        'hasNextPage' => new OA\Property(property: 'hasNextPage', type: 'boolean', example: true),
                        'hasPreviousPage' => new OA\Property(property: 'hasPreviousPage', type: 'boolean', example: false)
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function myClients(#[MapQueryString] ClientsFilterDTO $filterDto,Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);


            $listResponseDto = $this->clientService->getMyOfficeClients($currentUser, $filterDto);

            return new JsonResponse($listResponseDto->toArray());

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/details', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/clients/details',
        summary: 'Get client details',
        description: 'Get detailed information of a specific client by ID. Users can only access clients from their exchange office.',
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
        tags: ['Clients'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Client details retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'client' => new OA\Property(
                            property: 'client',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                'phone' => new OA\Property(property: 'phone', type: 'string'),
                                'nationality' => new OA\Property(property: 'nationality', type: 'string'),
                                'status' => new OA\Property(property: 'status', type: 'string'),
                                'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function show(#[MapQueryString] ClientIDQueryDTO $queryDto, Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);

            $id = $queryDto->clientId;

            $responseDto = $this->clientService->getClientDetails($id, $currentUser);

            return new JsonResponse([
                'client' => $responseDto->toArray()
            ]);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/clients',
        summary: 'Update a client',
        description: 'Update client information. Users can only update clients from their exchange office. All fields are optional. Note: exchangeOfficeId cannot be modified.',
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
        requestBody: new OA\RequestBody(
            description: 'Client data to update (all fields are optional)',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    'lastName' => new OA\Property(property: 'lastName', type: 'string', minLength: 2, maxLength: 100, description: 'Client last name', example: 'Dupont', nullable: true),
                    'firstName' => new OA\Property(property: 'firstName', type: 'string', minLength: 2, maxLength: 100, description: 'Client first name', example: 'Jean', nullable: true),
                    'birthDate' => new OA\Property(property: 'birthDate', type: 'string', format: 'date', description: 'Client birth date in YYYY-MM-DD format', example: '1990-01-15', nullable: true),
                    'email' => new OA\Property(property: 'email', type: 'string', format: 'email', description: 'Client email address', example: 'jean.dupont@email.com', nullable: true),
                    'phone' => new OA\Property(property: 'phone', type: 'string', pattern: '^\+?[0-9\s\-\(\)]{8,20}$', description: 'Client phone number', example: '+33123456789', nullable: true),
                    'whatsapp' => new OA\Property(property: 'whatsapp', type: 'string', pattern: '^\+?[0-9\s\-\(\)]{8,20}$', description: 'Client WhatsApp number', example: '+33123456789', nullable: true),
                    'nationalId' => new OA\Property(property: 'nationalId', type: 'string', description: 'National ID number', example: '1234567890123', nullable: true),
                    'passport' => new OA\Property(property: 'passport', type: 'string', description: 'Passport number', example: 'AB1234567', nullable: true),
                    'nationality' => new OA\Property(property: 'nationality', type: 'string', description: 'Client nationality', example: 'French', nullable: true),
                    'residence' => new OA\Property(property: 'residence', type: 'string', description: 'Client residence address', example: '123 Rue de la Paix, Paris', nullable: true),
                    'gender' => new OA\Property(property: 'gender', type: 'string', enum: ['male', 'female'], description: 'Client gender', example: 'male', nullable: true),
                    'acquisitionSource' => new OA\Property(property: 'acquisitionSource', type: 'string', enum: ['online', 'walk_in', 'referral', 'phone_call', 'email', 'social_media', 'advertising', 'partnership', 'agent_direct', 'other'], description: 'How the client was acquired', example: 'walk_in', nullable: true),
                    'status' => new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive'], description: 'Client status', example: 'active', nullable: true),
                    'currentSegment' => new OA\Property(property: 'currentSegment', type: 'string', description: 'Current client segment', example: 'VIP', nullable: true),
                ]
            )
        ),
        tags: ['Clients'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Client updated successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Client updated successfully'),
                        'client' => new OA\Property(
                            property: 'client',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                'phone' => new OA\Property(property: 'phone', type: 'string'),
                                'nationality' => new OA\Property(property: 'nationality', type: 'string'),
                                'status' => new OA\Property(property: 'status', type: 'string'),
                                'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function update(#[MapQueryString] ClientIDQueryDTO $queryDto,
    #[MapRequestPayload] UpdateClientDTO $dto,
    Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);

            $id = $queryDto->clientId;

            $responseDto = $this->clientService->updateClientFromDTO(
                $id,
                $dto,
                $currentUser
            );

            return new JsonResponse([
                'message' => 'Client updated successfully',
                'client' => $responseDto->toArray()
            ]);

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

    #[Route('', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/clients',
        summary: 'Delete a client',
        description: 'Soft delete a client by setting status to inactive. Users can only delete clients from their exchange office.',
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
        tags: ['Clients'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Client deleted successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Client deleted successfully')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function delete(#[MapQueryString] ClientIDQueryDTO $queryDto, Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);

            $id = $queryDto->clientId;
            $this->clientService->deleteClient($id, $currentUser);

            return new JsonResponse([
                'message' => 'Client deleted successfully'
            ]);

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

    #[Route('/groupby_exchange_office', name: 'group_by_exchange_office', methods: ['GET'])]
    #[OA\Get(
        path: '/api/clients/groupby_exchange_office',
        summary: 'Get all clients grouped by exchange office (Admin only)',
        description: 'Get a complete list of all exchange offices with all their respective clients. Only administrators can access this endpoint.',
        security: [['Bearer' => []]],
        tags: ['Clients'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'All clients grouped by exchange office retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'exchangeOffices' => new OA\Property(
                            property: 'exchangeOffices',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid', description: 'Exchange office UUID'),
                                    'name' => new OA\Property(property: 'name', type: 'string', description: 'Exchange office name'),
                                    'address' => new OA\Property(property: 'address', type: 'string', description: 'Exchange office address'),
                                    'email' => new OA\Property(property: 'email', type: 'string', format: 'email', description: 'Exchange office email'),
                                    'phone' => new OA\Property(property: 'phone', type: 'string', description: 'Exchange office phone'),
                                    'owner' => new OA\Property(property: 'owner', type: 'string', description: 'Exchange office owner'),
                                    'status' => new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive'], description: 'Exchange office status'),
                                    'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime', description: 'Creation date'),
                                    'totalClients' => new OA\Property(property: 'totalClients', type: 'integer', description: 'Number of clients in this office'),
                                    'clients' => new OA\Property(
                                        property: 'clients',
                                        type: 'array',
                                        items: new OA\Items(
                                            type: 'object',
                                            properties: [
                                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                                'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                                'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                                'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                                'phone' => new OA\Property(property: 'phone', type: 'string'),
                                                'nationality' => new OA\Property(property: 'nationality', type: 'string'),
                                                'status' => new OA\Property(property: 'status', type: 'string'),
                                                'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime')
                                            ]
                                        )
                                    )
                                ]
                            )
                        ),
                        'totalExchangeOffices' => new OA\Property(property: 'totalExchangeOffices', type: 'integer', description: 'Total number of exchange offices'),
                        'totalClientsAcrossAllOffices' => new OA\Property(property: 'totalClientsAcrossAllOffices', type: 'integer', description: 'Total number of clients across all offices')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function groupByExchangeOffice(Request $request): JsonResponse
    {
        try {
            $currentUser = $request->attributes->get('authenticated_user');
            
            if (!$currentUser) {
                $currentUser = $this->authService->getUserFromToken($request);
                if (!$currentUser) {
                    return new JsonResponse([
                        'error' => 'Access denied. Invalid token.'
                    ], Response::HTTP_UNAUTHORIZED);
                }
            }

            if ($currentUser->getRole()->value !== 'admin') {
                return new JsonResponse([
                    'error' => 'Only administrators can view all clients grouped by exchange office'
                ], Response::HTTP_FORBIDDEN);
            }

            $allExchangeOfficesWithClientsDto = $this->exchangeOfficeService->getAllExchangeOfficesWithClients($currentUser);

            return new JsonResponse($allExchangeOfficesWithClientsDto->toArray());

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

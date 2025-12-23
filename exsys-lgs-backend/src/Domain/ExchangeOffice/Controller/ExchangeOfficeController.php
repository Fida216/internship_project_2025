<?php

namespace App\Domain\ExchangeOffice\Controller;

use App\Domain\ExchangeOffice\DTO\ExchangeOfficeIDQueryDTO;
use App\Domain\ExchangeOffice\DTO\ListExchangeOfficeQueryDTO;
use App\Domain\ExchangeOffice\DTO\CreateExchangeOfficeDTO;
use App\Domain\ExchangeOffice\DTO\UpdateExchangeOfficeDTO;
use App\Domain\ExchangeOffice\DTO\UpdateExchangeOfficeStatusDTO;
use App\Domain\ExchangeOffice\Service\ExchangeOfficeService;
use App\Domain\Auth\Service\AuthService;
use App\Shared\Service\ErrorCodeResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;


use OpenApi\Attributes as OA;

#[Route('/api/exchange-offices', name: 'api_exchange_office_')]
class ExchangeOfficeController extends AbstractController
{
    private ExchangeOfficeService $exchangeOfficeService;
    private AuthService $authService;
    private ErrorCodeResolver $errorCodeResolver;

    public function __construct(
        ExchangeOfficeService $exchangeOfficeService, 
        AuthService $authService,
        ErrorCodeResolver $errorCodeResolver
    ) {
        $this->exchangeOfficeService = $exchangeOfficeService;
        $this->authService = $authService;
        $this->errorCodeResolver = $errorCodeResolver;
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/exchange-offices',
        summary: 'Create a new exchange office',
        description: 'Create a new exchange office. Only administrators can create exchange offices.',
        security: [['Bearer' => []]],
        tags: ['Exchange Offices']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['name', 'address', 'email', 'phone', 'owner'],
            properties: [
                new OA\Property(property: 'name', type: 'string', minLength: 2, maxLength: 150, example: 'Bureau Central'),
                new OA\Property(property: 'address', type: 'string', minLength: 10, maxLength: 255, example: '123 Main Street, City Center'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'bureau.central@example.com'),
                new OA\Property(property: 'phone', type: 'string', example: '+33123456789'),
                new OA\Property(property: 'owner', type: 'string', minLength: 2, maxLength: 100, example: 'John Doe')
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Exchange office created successfully',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Exchange office created successfully'),
                new OA\Property(
                    property: 'exchangeOffice',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: '550e8400-e29b-41d4-a716-446655440000'),
                        new OA\Property(property: 'name', type: 'string', example: 'Bureau Central'),
                        new OA\Property(property: 'address', type: 'string', example: '123 Main Street, City Center'),
                        new OA\Property(property: 'email', type: 'string', example: 'bureau.central@example.com'),
                        new OA\Property(property: 'phone', type: 'string', example: '+33123456789'),
                        new OA\Property(property: 'owner', type: 'string', example: 'John Doe'),
                        new OA\Property(property: 'status', type: 'string', example: 'active'),
                        new OA\Property(property: 'createdAt', type: 'string', example: '2024-01-15')
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Invalid data or validation errors')]
    #[OA\Response(response: 401, description: 'Missing or invalid authentication token')]
    #[OA\Response(response: 403, description: 'Only administrators can create exchange offices')]
    #[OA\Response(response: 409, description: 'Exchange office with this email already exists')]

    public function createExchangeOffice(#[MapRequestPayload] CreateExchangeOfficeDTO $dto): JsonResponse
    {
        try {

            $responseDto = $this->exchangeOfficeService->createExchangeOfficeFromDto($dto);

            return new JsonResponse([
                'message' => 'Exchange office created successfully',
                'exchangeOffice' => $responseDto->toArray()
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

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/exchange-offices',
        summary: 'List exchange offices or get specific office',
        description: 'Get list of exchange offices with optional filters, or get a specific office by ID. Only administrators can access this.',
        security: [['Bearer' => []]],
        tags: ['Exchange Offices']
    )]
    #[OA\Parameter(
        name: 'status',
        in: 'query',
        description: 'Filter by status (active, inactive). If not provided, returns all statuses.',
        required: false,
        schema: new OA\Schema(
            type: 'string',
            enum: ['active', 'inactive']
        )
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'Get a specific exchange office by ID. If provided, other filters are ignored.',
        required: false,
        schema: new OA\Schema(
            type: 'string',
            format: 'uuid'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'List of exchange offices or specific office details',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(
                    property: 'exchangeOffices',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: '550e8400-e29b-41d4-a716-446655440000'),
                            new OA\Property(property: 'name', type: 'string', example: 'Bureau Central'),
                            new OA\Property(property: 'address', type: 'string', example: '123 Main Street, City Center'),
                            new OA\Property(property: 'email', type: 'string', example: 'bureau.central@example.com'),
                            new OA\Property(property: 'phone', type: 'string', example: '+33123456789'),
                            new OA\Property(property: 'owner', type: 'string', example: 'John Doe'),
                            new OA\Property(property: 'status', type: 'string', example: 'active'),
                            new OA\Property(property: 'createdAt', type: 'string', example: '2024-01-15')
                        ]
                    )
                ),
                new OA\Property(property: 'total', type: 'integer', example: 5),
                new OA\Property(
                    property: 'filters',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'string', nullable: true, example: 'active'),
                        new OA\Property(property: 'id', type: 'string', nullable: true, example: '550e8400-e29b-41d4-a716-446655440000')
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Invalid filter parameters')]
    #[OA\Response(response: 401, description: 'Missing or invalid authentication token')]
    #[OA\Response(response: 403, description: 'Only administrators can access this resource')]
    #[OA\Response(response: 404, description: 'Exchange office not found (when using ID parameter)')]
    public function listExchangeOffices(#[MapQueryString] ListExchangeOfficeQueryDTO $filters): JsonResponse
    {
        try {

            $statusFilter = $filters->status;
            $idFilter = $filters->id;

            if ($idFilter) {
                try {
                    $responseDto = $this->exchangeOfficeService->getExchangeOfficeDetails($idFilter);
                    return new JsonResponse([
                        'exchangeOffices' => [$responseDto->toArray()],
                        'total' => 1,
                        'filters' => ['id' => $idFilter]
                    ]);
                } catch (\InvalidArgumentException $e) {
                    return new JsonResponse([
                        'error' => $e->getMessage()
                    ], Response::HTTP_NOT_FOUND);
                }
            }

            if ($statusFilter && !in_array($statusFilter, ['active', 'inactive'])) {
                return new JsonResponse([
                    'error' => 'Invalid status filter. Allowed values: active, inactive'
                ], Response::HTTP_BAD_REQUEST);
            }

            $listResponseDto = $this->exchangeOfficeService->getAllExchangeOfficesWithFilters( [
                'status' => $statusFilter
            ]);

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

    #[Route('', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/exchange-offices',
        summary: 'Update an exchange office',
        description: 'Update exchange office information using query parameter. Only administrators can update exchange offices.',
        security: [['Bearer' => []]],
        tags: ['Exchange Offices']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'ID of the exchange office to update',
        required: true,
        schema: new OA\Schema(
            type: 'string',
            format: 'uuid'
        )
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'name', type: 'string', minLength: 2, maxLength: 150, example: 'Bureau Central Updated'),
                new OA\Property(property: 'address', type: 'string', minLength: 10, maxLength: 255, example: '123 Updated Street, City Center'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'updated.bureau@example.com'),
                new OA\Property(property: 'phone', type: 'string', example: '+33123456789'),
                new OA\Property(property: 'owner', type: 'string', minLength: 2, maxLength: 100, example: 'Jane Doe'),
                new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive'], example: 'active', description: 'Status of the exchange office')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Exchange office updated successfully',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Exchange office updated successfully'),
                new OA\Property(
                    property: 'exchangeOffice',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: '550e8400-e29b-41d4-a716-446655440000'),
                        new OA\Property(property: 'name', type: 'string', example: 'Bureau Central Updated'),
                        new OA\Property(property: 'address', type: 'string', example: '123 Updated Street, City Center'),
                        new OA\Property(property: 'email', type: 'string', example: 'updated.bureau@example.com'),
                        new OA\Property(property: 'phone', type: 'string', example: '+33123456789'),
                        new OA\Property(property: 'owner', type: 'string', example: 'Jane Doe'),
                        new OA\Property(property: 'status', type: 'string', example: 'active'),
                        new OA\Property(property: 'createdAt', type: 'string', example: '2024-01-15')
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Invalid data, validation errors, or missing ID parameter')]
    #[OA\Response(response: 401, description: 'Missing or invalid authentication token')]
    #[OA\Response(response: 403, description: 'Only administrators can update exchange offices')]
    #[OA\Response(response: 404, description: 'Exchange office not found')]
    public function update(#[MapQueryString] ExchangeOfficeIDQueryDTO $queryDto,
    #[MapRequestPayload] UpdateExchangeOfficeDTO $dto): JsonResponse
    {
        try {

            $responseDto = $this->exchangeOfficeService->updateExchangeOfficeFromDto($queryDto->id, $dto);
            return new JsonResponse([
                'message' => 'Exchange office updated successfully',
                'exchangeOffice' => $responseDto->toArray()
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

    #[Route('/status', name: 'update_status', methods: ['PATCH'])]
    #[OA\Patch(
        path: '/api/exchange-offices/status',
        summary: 'Update exchange office status',
        description: 'Update the status (active/inactive) of an exchange office. Only administrators can update exchange office status.',
        security: [['Bearer' => []]],
        tags: ['Exchange Offices'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID of the exchange office to update',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
                example: '123e4567-e89b-12d3-a456-426614174000'
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    'status' => new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['active', 'inactive'],
                        description: 'New status for the exchange office',
                        example: 'active'
                    )
                ],
                required: ['status']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Exchange office status updated successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Exchange office status updated successfully'
                        ),
                        'exchangeOffice' => new OA\Property(
                            property: 'exchangeOffice',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'name' => new OA\Property(property: 'name', type: 'string'),
                                'address' => new OA\Property(property: 'address', type: 'string'),
                                'phone' => new OA\Property(property: 'phone', type: 'string'),
                                'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                'owner' => new OA\Property(property: 'owner', type: 'string'),
                                'status' => new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive']),
                                'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Invalid input data'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden - Only administrators can update exchange office status'),
            new OA\Response(response: 404, description: 'Exchange office not found')
        ]
    )]
    public function updateExchangeOfficeStatus(#[MapQueryString] ExchangeOfficeIDQueryDTO $queryDto,
    #[MapRequestPayload] UpdateExchangeOfficeStatusDTO $dto): JsonResponse
    {
        try {
            $result = $this->exchangeOfficeService->updateExchangeOfficeStatusFromDto($queryDto->id, $dto);

            return new JsonResponse([
                'message' => 'Exchange office status updated successfully',
                'exchangeOffice' => $result->toArray(),
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
        path: '/api/exchange-offices',
        summary: 'Delete an exchange office',
        description: 'Soft delete an exchange office using query parameter (set status to inactive). Only administrators can delete exchange offices.',
        security: [['Bearer' => []]],
        tags: ['Exchange Offices']
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'query',
        description: 'ID of the exchange office to delete',
        required: true,
        schema: new OA\Schema(
            type: 'string',
            format: 'uuid'
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Exchange office deleted successfully',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Exchange office deleted successfully')
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Missing ID parameter')]
    #[OA\Response(response: 401, description: 'Missing or invalid authentication token')]
    #[OA\Response(response: 403, description: 'Only administrators can delete exchange offices')]
    #[OA\Response(response: 404, description: 'Exchange office not found')]
    #[OA\Response(response: 409, description: 'Cannot delete exchange office: it has associated users or clients')]
    public function delete(#[MapQueryString] ExchangeOfficeIDQueryDTO $queryDto): JsonResponse
    {
        try {
            $this->exchangeOfficeService->deleteExchangeOffice($queryDto->id);

            return new JsonResponse([
                'message' => 'Exchange office deleted successfully'
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

    #[Route('/my-office', name: 'my_office', methods: ['GET'])]
    #[OA\Get(
        path: '/api/exchange-offices/my-office',
        summary: 'Get connected agent\'s exchange office information',
        description: 'Get information about the exchange office of the currently connected agent. Only agents can access this endpoint.',
        security: [['Bearer' => []]],
        tags: ['Exchange Offices']
    )]
    #[OA\Response(
        response: 200,
        description: 'Exchange office information retrieved successfully',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(
                    property: 'exchangeOffice',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: '550e8400-e29b-41d4-a716-446655440000'),
                        new OA\Property(property: 'name', type: 'string', example: 'Bureau Central'),
                        new OA\Property(property: 'address', type: 'string', example: '123 Main Street, City Center'),
                        new OA\Property(property: 'email', type: 'string', example: 'bureau.central@example.com'),
                        new OA\Property(property: 'phone', type: 'string', example: '+33123456789'),
                        new OA\Property(property: 'owner', type: 'string', example: 'John Doe'),
                        new OA\Property(property: 'status', type: 'string', example: 'active'),
                        new OA\Property(property: 'createdAt', type: 'string', example: '2024-01-15')
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Missing or invalid authentication token')]
    #[OA\Response(response: 403, description: 'Only agents can access this resource')]
    #[OA\Response(response: 404, description: 'Agent is not assigned to any exchange office')]
    public function getMyExchangeOffice(Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);


            $responseDto = $this->exchangeOfficeService->getAgentExchangeOffice($currentUser);

            return new JsonResponse([
                'exchangeOffice' => $responseDto->toArray()
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
}

<?php

namespace App\Domain\User\Controller;

use App\Domain\User\Service\UserService;
use App\Domain\Auth\Service\AuthService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Domain\User\DTO\CreateUserDTO;
use App\Domain\User\DTO\UpdateUserStatusDTO;
use App\Domain\User\DTO\UserIDQueryDTO;
use App\Domain\User\DTO\ChangePasswordDTO;
use App\Domain\User\DTO\ResetPasswordDTO;
use App\Domain\User\DTO\UpdateUserDTO;
use App\Domain\User\DTO\UserFilterDTO;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    private UserService $userService;
    private AuthService $authService;

    public function __construct(
        UserService $userService, 
        AuthService $authService
    ) {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/users',
        summary: 'Create a new user',
        description: 'Create a new user (admin or agent). Only administrators can create users.',
        security: [['Bearer' => []]],
        tags: ['Users'],
        requestBody: new OA\RequestBody(
            description: 'User data',
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['lastName', 'firstName', 'phone', 'role', 'email', 'password'],
                properties: [
                    'lastName' => new OA\Property(
                        property: 'lastName', 
                        type: 'string', 
                        maxLength: 100,
                        description: 'User last name',
                        example: 'Doe'
                    ),
                    'firstName' => new OA\Property(
                        property: 'firstName', 
                        type: 'string', 
                        maxLength: 100,
                        description: 'User first name',
                        example: 'John'
                    ),
                    'phone' => new OA\Property(
                        property: 'phone', 
                        type: 'string',
                        pattern: '^\+?[1-9]\d{1,14}$',
                        description: 'User phone number in international format',
                        example: '+1234567890'
                    ),
                    'role' => new OA\Property(
                        property: 'role', 
                        type: 'string', 
                        enum: ['admin', 'agent'],
                        description: 'User role',
                        example: 'agent'
                    ),
                    'email' => new OA\Property(
                        property: 'email', 
                        type: 'string', 
                        format: 'email',
                        description: 'User email address',
                        example: 'john.doe@example.com'
                    ),
                    'password' => new OA\Property(
                        property: 'password', 
                        type: 'string',
                        minLength: 6,
                        description: 'User password (minimum 6 characters)',
                        example: 'password123'
                    ),
                    'exchangeOfficeId' => new OA\Property(
                        property: 'exchangeOfficeId', 
                        type: 'string', 
                        format: 'uuid',
                        nullable: true,
                        description: 'Exchange office ID (required for agents only)',
                        example: '123e4567-e89b-12d3-a456-426614174000'
                    ),
                    'status' => new OA\Property(
                        property: 'status', 
                        type: 'string', 
                        enum: ['active', 'inactive'],
                        description: 'User status (default: active)',
                        example: 'active'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User created successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'User created successfully'),
                        'user' => new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                'phone' => new OA\Property(property: 'phone', type: 'string'),
                                'role' => new OA\Property(property: 'role', type: 'string', enum: ['admin', 'agent']),
                                'status' => new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive']),
                                'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime'),
                                'exchangeOffice' => new OA\Property(
                                    property: 'exchangeOffice',
                                    type: 'object',
                                    nullable: true,
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
            new OA\Response(response: 400, description: 'Invalid data or validation errors'),
            new OA\Response(response: 401, description: 'Unauthorized - invalid or missing token'),
            new OA\Response(response: 403, description: 'Forbidden - insufficient permissions'),
            new OA\Response(response: 409, description: 'Conflict - user already exists')
        ]
    )]
    public function create(#[MapRequestPayload] CreateUserDTO $dto): JsonResponse
    {
        try {
            $result = $this->userService->createUserFromDto($dto);

            return new JsonResponse([
                'message' => 'User created successfully',
                'user' => $result->toArray(),
            ], Response::HTTP_CREATED);

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->userService->getErrorStatusCode($e->getMessage());
            
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
        path: '/api/users',
        summary: 'Get all users',
        description: 'Get list of all users. Administrators can see all users, agents can only see users from their exchange office (no filter).',
        security: [['Bearer' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'exchangeOfficeId',
                description: 'Filter by exchange office ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
                example: '123e4567-e89b-12d3-a456-426614174000'
            ),
            new OA\Parameter(
                name: 'role',
                description: 'Filter by user role',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['admin', 'agent']),
                example: 'agent'
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Filter by user status',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['active', 'inactive']),
                example: 'active'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Users retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'users' => new OA\Property(
                            property: 'users',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                    'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                    'phone' => new OA\Property(property: 'phone', type: 'string'),
                                    'role' => new OA\Property(property: 'role', type: 'string', enum: ['admin', 'agent']),
                                    'status' => new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive']),
                                    'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                    'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime'),
                                    'exchangeOffice' => new OA\Property(
                                        property: 'exchangeOffice',
                                        type: 'object',
                                        nullable: true,
                                        properties: [
                                            'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                            'name' => new OA\Property(property: 'name', type: 'string')
                                        ]
                                    )
                                ]
                            )
                        ),
                        'total' => new OA\Property(property: 'total', type: 'integer', example: 25)
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized - invalid or missing token'),
            new OA\Response(response: 403, description: 'Forbidden - insufficient permissions')
        ]
    )]
    public function list(   #[MapQueryString] UserFilterDTO $filterDto, Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);
            $result = $this->userService->getAllUsersFromRequest($currentUser, $filterDto);

            return new JsonResponse($result->toArray());

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->userService->getErrorStatusCode($e->getMessage());
            
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

    #[Route('/agents/grouped-by-exchange-office', name: 'agents_grouped_by_exchange_office', methods: ['GET'])]
    #[OA\Get(
        path: '/api/users/agents/grouped-by-exchange-office',
        summary: 'Get agents grouped by exchange office',
        description: 'Get all exchange offices with their complete information and associated agents. Only administrators can access this endpoint.',
        security: [['Bearer' => []]],
        tags: ['Users'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Agents grouped by exchange office retrieved successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            'exchangeOffice' => new OA\Property(
                                property: 'exchangeOffice',
                                type: 'object',
                                nullable: true,
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
                            ),
                            'agents' => new OA\Property(
                                property: 'agents',
                                type: 'array',
                                items: new OA\Items(
                                    type: 'object',
                                    properties: [
                                        'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                        'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                        'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                        'phone' => new OA\Property(property: 'phone', type: 'string'),
                                        'role' => new OA\Property(property: 'role', type: 'string', enum: ['agent']),
                                        'status' => new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive']),
                                        'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                        'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime'),
                                        'exchangeOffice' => new OA\Property(
                                            property: 'exchangeOffice',
                                            type: 'object',
                                            nullable: true,
                                            properties: [
                                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                                'name' => new OA\Property(property: 'name', type: 'string')
                                            ]
                                        )
                                    ]
                                )
                            )
                        ]
                    ),
                    example: [
                        [
                            'exchangeOffice' => [
                                'id' => '123e4567-e89b-12d3-a456-426614174001',
                                'name' => 'Exchange Office Downtown',
                                'address' => '123 Main St, City Center',
                                'phone' => '+1234567890',
                                'email' => 'downtown@exchange.com',
                                'owner' => 'John Smith',
                                'status' => 'active',
                                'createdAt' => '2024-01-15'
                            ],
                            'agents' => [
                                [
                                    'id' => '123e4567-e89b-12d3-a456-426614174000',
                                    'lastName' => 'Doe',
                                    'firstName' => 'John',
                                    'phone' => '+1234567890',
                                    'role' => 'agent',
                                    'status' => 'active',
                                    'email' => 'john.doe@example.com',
                                    'createdAt' => '2024-01-15',
                                    'exchangeOffice' => [
                                        'id' => '123e4567-e89b-12d3-a456-426614174001',
                                        'name' => 'Exchange Office Downtown'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'exchangeOffice' => [
                                'id' => '123e4567-e89b-12d3-a456-426614174002',
                                'name' => 'Exchange Office Uptown',
                                'address' => '456 Oak Avenue, Uptown',
                                'phone' => '+1234567891',
                                'email' => 'uptown@exchange.com',
                                'owner' => 'Jane Wilson',
                                'status' => 'active',
                                'createdAt' => '2024-02-01'
                            ],
                            'agents' => []
                        ]
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden - Only administrators can access this endpoint')
        ]
    )]
    public function getAgentsGroupedByExchangeOffice(): JsonResponse
    {
        try {
            $result = $this->userService->getGroupedAgentsByExchangeOffice();

            return new JsonResponse($result);

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->userService->getErrorStatusCode($e->getMessage());
            
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

    #[Route('/status', name: 'update_user_status', methods: ['PATCH'])]
    #[OA\Patch(
        path: '/api/users/status',
        summary: 'Update user status',
        description: 'Update the status (active/inactive) of a user. Only administrators can update user status.',
        security: [['Bearer' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'userId',
                description: 'ID of the user to update',
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
                        description: 'New status for the user',
                        example: 'active'
                    )
                ],
                required: ['status']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User status updated successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'User status updated successfully'
                        ),
                        'user' => new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                'phone' => new OA\Property(property: 'phone', type: 'string'),
                                'role' => new OA\Property(property: 'role', type: 'string', enum: ['admin', 'agent']),
                                'status' => new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive']),
                                'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime'),
                                'exchangeOffice' => new OA\Property(
                                    property: 'exchangeOffice',
                                    type: 'object',
                                    nullable: true,
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
            new OA\Response(response: 400, description: 'Invalid input data'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden - Only administrators can update user status'),
            new OA\Response(response: 404, description: 'User not found')
        ]
    )]
    public function updateUserStatus(#[MapQueryString] UserIDQueryDTO $queryDto,
    #[MapRequestPayload] UpdateUserStatusDTO $payloadDto): JsonResponse
    {
        try {

            $result = $this->userService->updateUserStatus($queryDto->userId, $payloadDto->status);

            return new JsonResponse([
                'message' => 'User status updated successfully',
                'user' => $result->toArray(),
            ]);

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->userService->getErrorStatusCode($e->getMessage());
            
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

    #[Route('/change-password', name: 'change_password', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/users/change-password',
        summary: 'Change user password',
        description: 'Change the password of the authenticated user. User must provide old password for verification.',
        security: [['Bearer' => []]],
        tags: ['Users'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    'oldPassword' => new OA\Property(
                        property: 'oldPassword',
                        type: 'string',
                        description: 'Current password for verification',
                        example: 'currentPassword123'
                    ),
                    'newPassword' => new OA\Property(
                        property: 'newPassword',
                        type: 'string',
                        minLength: 6,
                        description: 'New password (minimum 6 characters)',
                        example: 'newPassword123'
                    )
                ],
                required: ['oldPassword', 'newPassword']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Password changed successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Password changed successfully'
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Invalid input data or incorrect old password'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public function changePassword(#[MapRequestPayload] ChangePasswordDTO $dto,Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);
        $result = $this->userService->changePassword($dto, $currentUser);
        
            return new JsonResponse($result->toArray());

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->userService->getErrorStatusCode($e->getMessage());
            
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

    #[Route('/reset-password', name: 'reset_password', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/users/reset-password',
        summary: 'Reset user password',
        description: 'Reset the password of any user. Only administrators can reset passwords.',
        security: [['Bearer' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'userId',
                description: 'ID of the user whose password will be reset',
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
                    'newPassword' => new OA\Property(
                        property: 'newPassword',
                        type: 'string',
                        minLength: 8,
                        description: 'New password (minimum 8 characters)',
                        example: 'newPassword123'
                    )
                ],
                required: ['newPassword']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Password reset successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Password reset successfully'
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Invalid input data'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden - Only administrators can reset passwords'),
            new OA\Response(response: 404, description: 'User not found')
        ]
    )]
    public function resetPassword(#[MapQueryString] UserIDQueryDTO $queryDto, #[MapRequestPayload] ResetPasswordDTO $resetPasswordDto): JsonResponse
    {
        try {
            $userId = $queryDto->userId;

            $result = $this->userService->resetPassword($userId, $resetPasswordDto);

            return new JsonResponse($result->toArray());

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->userService->getErrorStatusCode($e->getMessage());
            
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

    #[Route('/update', name: 'update_user', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/users/update',
        summary: 'Update user information',
        description: 'Update user information. Only administrators can update users.',
        security: [['Bearer' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(
                name: 'userId',
                description: 'ID of the user to update',
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
                    'lastName' => new OA\Property(
                        property: 'lastName',
                        type: 'string',
                        maxLength: 255,
                        description: 'User last name',
                        example: 'Smith'
                    ),
                    'firstName' => new OA\Property(
                        property: 'firstName',
                        type: 'string',
                        maxLength: 255,
                        description: 'User first name',
                        example: 'John'
                    ),
                    'phone' => new OA\Property(
                        property: 'phone',
                        type: 'string',
                        maxLength: 20,
                        description: 'User phone number',
                        example: '+1234567890'
                    ),
                    'email' => new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        maxLength: 255,
                        description: 'User email address',
                        example: 'john.smith@example.com'
                    ),
                    'status' => new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['active', 'inactive'],
                        description: 'User status',
                        example: 'active'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User updated successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'User updated successfully'
                        ),
                        'user' => new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                'lastName' => new OA\Property(property: 'lastName', type: 'string'),
                                'firstName' => new OA\Property(property: 'firstName', type: 'string'),
                                'phone' => new OA\Property(property: 'phone', type: 'string'),
                                'role' => new OA\Property(property: 'role', type: 'string', enum: ['admin', 'agent']),
                                'status' => new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive']),
                                'email' => new OA\Property(property: 'email', type: 'string', format: 'email'),
                                'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'datetime'),
                                'exchangeOffice' => new OA\Property(
                                    property: 'exchangeOffice',
                                    type: 'object',
                                    nullable: true,
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
            new OA\Response(response: 400, description: 'Invalid input data'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden - Only administrators can update users'),
            new OA\Response(response: 404, description: 'User not found'),
            new OA\Response(response: 409, description: 'Email already exists')
        ]
    )]
    public function updateUser(
        #[MapQueryString] UserIDQueryDTO $queryDto,
        #[MapRequestPayload] UpdateUserDTO $dto): JsonResponse
    {
        try {
            $userId = $queryDto->userId;

            $result = $this->userService->updateUser($userId, $dto);

            return new JsonResponse([
                'message' => 'User updated successfully',
                'user' => $result->toArray(),
            ]);

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->userService->getErrorStatusCode($e->getMessage());
            
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

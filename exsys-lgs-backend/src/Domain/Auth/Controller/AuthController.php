<?php

namespace App\Domain\Auth\Controller;

use App\Domain\Auth\Service\AuthService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Domain\Auth\DTO\LoginDTO;


#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/auth/login',
        summary: 'User authentication',
        description: 'Authenticate a user with email and password and return a JWT token',
        tags: ['Authentication']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@gmail.com'),
                new OA\Property(property: 'password', type: 'string', example: 'password123')
            ]
        )
    )]
        #[OA\Response(
        response: 200,
        description: 'Authentication successful',
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(
                    property: 'message',
                    type: 'string',
                    example: 'Login successful'
                ),
                new OA\Property(
                    property: 'token',
                    type: 'string',
                    description: 'JWT token for authentication',
                    example: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...'
                ),
                new OA\Property(
                    property: 'user',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'email', type: 'string', example: 'john.doe@example.com'),
                        new OA\Property(property: 'firstName', type: 'string', example: 'John'),
                        new OA\Property(property: 'lastName', type: 'string', example: 'Doe'),
                        new OA\Property(property: 'role', type: 'string', example: 'admin'),
                        new OA\Property(
                            property: 'exchangeOffice',
                            type: 'object',
                            nullable: true,
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Bureau Central')
                            ]
                        )
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Invalid data')]
    #[OA\Response(response: 401, description: 'Invalid credentials')]
    #[OA\Response(response: 403, description: 'Account disabled')]
    public function login(#[MapRequestPayload] LoginDTO $loginDto): JsonResponse
    {
        try {
        $result = $this->authService->authenticate($loginDto);

            return new JsonResponse([
                'message' => 'Login successful',
                'token' => $result['token'],
                'user' => $result['user'],
                'expires_in' => $result['expires_in'],
            ]);

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->authService->getErrorStatusCode($e->getMessage());
            
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

    #[Route('/me', name: 'me', methods: ['GET'])]
    #[OA\Get(
        path: '/api/auth/me',
        summary: 'Get current user profile',
        description: 'Return the authenticated user information based on JWT token',
        security: [['Bearer' => []]],
        tags: ['Authentication']
    )]
    #[OA\Response(
        response: 200,
        description: 'User profile retrieved successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'user',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: '550e8400-e29b-41d4-a716-446655440000'),
                        new OA\Property(property: 'email', type: 'string', example: 'john.doe@example.com'),
                        new OA\Property(property: 'firstName', type: 'string', example: 'John'),
                        new OA\Property(property: 'lastName', type: 'string', example: 'Doe'),
                        new OA\Property(property: 'phone', type: 'string', example: '+33123456789'),
                        new OA\Property(property: 'role', type: 'string', example: 'admin'),
                        new OA\Property(property: 'status', type: 'string', example: 'active'),
                        new OA\Property(property: 'createdAt', type: 'string', format: 'datetime', example: '2024-01-15 10:30:00'),
                        new OA\Property(
                            property: 'exchangeOffice',
                            type: 'object',
                            nullable: true,
                            properties: [
                                new OA\Property(property: 'id', type: 'string', example: '550e8400-e29b-41d4-a716-446655440001'),
                                new OA\Property(property: 'name', type: 'string', example: 'Bureau Central')
                            ]
                        )
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Invalid or expired token')]
    public function getCurrentUser(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->getCurrentUserFromRequest($request);
            
            return new JsonResponse($result);

        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->authService->getErrorStatusCode($e->getMessage());
            
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

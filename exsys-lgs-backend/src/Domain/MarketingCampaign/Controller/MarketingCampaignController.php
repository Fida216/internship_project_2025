<?php

namespace App\Domain\MarketingCampaign\Controller;

use App\Domain\MarketingCampaign\DTO\CreateMarketingCampaignDTO;
use App\Domain\MarketingCampaign\DTO\UpdateCampaignStatusRequestDTO;
use App\Domain\MarketingCampaign\DTO\MarketingCampaignQueryDTO;
use App\Domain\MarketingCampaign\DTO\ManageTargetClientsDTO;
use App\Domain\MarketingCampaign\Service\MarketingCampaignService;
use App\Domain\Auth\Service\AuthService;
use App\Shared\Service\ErrorCodeResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api/marketing-campaigns', name: 'api_marketing_campaigns_')]
#[OA\Tag(name: 'Marketing Campaigns')]
class MarketingCampaignController extends AbstractController
{
    public function __construct(
        private MarketingCampaignService $campaignService,
        private AuthService $authService,
        private ErrorCodeResolver $errorCodeResolver
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/marketing-campaigns',
        summary: 'Create a new marketing campaign',
        description: 'Create a new marketing campaign with multiple target clients for an exchange office. You can specify one or more clients in the targets array. Agents can only create campaigns for their own exchange office.',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', description: 'Campaign title', example: 'Summer Promotion 2024'),
                    new OA\Property(property: 'description', type: 'string', description: 'Campaign description', example: 'Special summer rates for currency exchange'),
                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'active', 'completed', 'cancelled'], description: 'Campaign status', example: 'draft'),
                    new OA\Property(property: 'startDate', type: 'string', format: 'date', description: 'Campaign start date', example: '2024-06-01'),
                    new OA\Property(property: 'endDate', type: 'string', format: 'date', description: 'Campaign end date', example: '2024-08-31'),
                    new OA\Property(
                        property: 'targets',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'clientId', type: 'string', format: 'uuid', description: 'Target client UUID', example: '123e4567-e89b-12d3-a456-426614174000')
                            ]
                        ),
                        description: 'Array of target clients for the campaign. You can specify multiple clients.',
                        example: [
                            ['clientId' => '123e4567-e89b-12d3-a456-426614174000'],
                            ['clientId' => '987fcdeb-51a2-43d1-9876-ba9876543210'],
                            ['clientId' => '456789ab-cdef-1234-5678-90abcdef1234']
                        ]
                    )
                ],
                required: ['title', 'description', 'status', 'startDate', 'endDate', 'targets'],
                example: [
                    'title' => 'Summer Promotion 2024',
                    'description' => 'Special summer rates for currency exchange',
                    'status' => 'draft',
                    'startDate' => '2024-06-01',
                    'endDate' => '2024-08-31',
                    'targets' => [
                        ['clientId' => '123e4567-e89b-12d3-a456-426614174000'],
                        ['clientId' => '987fcdeb-51a2-43d1-9876-ba9876543210'],
                        ['clientId' => '456789ab-cdef-1234-5678-90abcdef1234']
                    ]
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Marketing campaign created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Marketing campaign created successfully'),
                        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(property: 'startDate', type: 'string', format: 'date'),
                        new OA\Property(property: 'endDate', type: 'string', format: 'date'),
                        new OA\Property(property: 'createdAt', type: 'string', format: 'datetime')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request - Invalid data or validation errors'),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - Insufficient permissions'),
            new OA\Response(response: 404, description: 'Not found - One or more clients not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function create(#[MapRequestPayload] CreateMarketingCampaignDTO $dto, Request $request): JsonResponse {
        try {
            // Get the current authenticated user
            $currentUser = $this->authService->getUserFromToken($request);

            // Create the marketing campaign
            $responseDto = $this->campaignService->createMarketingCampaignFromDto($dto, $currentUser);

            $response = $responseDto->toArray();
            $response['message'] = 'Marketing campaign created successfully';

            return new JsonResponse(
                $response,
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

    #[Route('', name: 'get', methods: ['GET'])]
    #[OA\Get(
        path: '/api/marketing-campaigns',
        summary: 'Get a marketing campaign by ID with marketing actions',
        description: 'Retrieve a specific marketing campaign, its target clients, and all associated marketing actions. Users can only access campaigns from their own exchange office.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'campaignId',
                in: 'query',
                required: true,
                description: 'Marketing campaign UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Marketing campaign retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(property: 'startDate', type: 'string', format: 'date'),
                        new OA\Property(property: 'endDate', type: 'string', format: 'date'),
                        new OA\Property(property: 'createdAt', type: 'string', format: 'datetime'),
                        new OA\Property(
                            property: 'targetClients',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    new OA\Property(property: 'firstName', type: 'string'),
                                    new OA\Property(property: 'lastName', type: 'string')
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'marketingActions',
                            type: 'array',
                            description: 'List of marketing actions associated with this campaign',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid', description: 'Marketing action unique identifier'),
                                    new OA\Property(property: 'title', type: 'string', example: 'Welcome Email', description: 'Marketing action title'),
                                    new OA\Property(property: 'channelType', type: 'string', enum: ['email', 'sms', 'whatsapp'], example: 'email', description: 'Communication channel'),
                                    new OA\Property(property: 'content', type: 'string', example: 'Welcome to our premium service!', description: 'Marketing message content'),
                                    new OA\Property(property: 'createdAt', type: 'string', format: 'datetime', description: 'Action creation timestamp')
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request - Invalid campaign ID'),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - Campaign does not belong to your exchange office'),
            new OA\Response(response: 404, description: 'Not found - Marketing campaign not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function get(#[MapQueryString] MarketingCampaignQueryDTO $queryDto, Request $request): JsonResponse {
        try {
            $currentUser = $this->authService->getUserFromToken($request);

            $responseDto = $this->campaignService->getMarketingCampaignById($queryDto->campaignId, $currentUser);

            return new JsonResponse(
                $responseDto->toArray(),
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

    #[Route('/list', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/marketing-campaigns/list',
        summary: 'List all marketing campaigns for exchange office',
        description: 'Retrieve all marketing campaigns for the authenticated user\'s exchange office.',
        security: [['Bearer' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Marketing campaigns retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'campaigns',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    new OA\Property(property: 'title', type: 'string'),
                                    new OA\Property(property: 'status', type: 'string'),
                                    new OA\Property(property: 'startDate', type: 'string', format: 'date'),
                                    new OA\Property(property: 'endDate', type: 'string', format: 'date'),
                                    new OA\Property(property: 'targetClientCount', type: 'integer'),
                                    new OA\Property(property: 'createdAt', type: 'string', format: 'datetime')
                                ]
                            )
                        ),
                        new OA\Property(property: 'total', type: 'integer')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - User not assigned to exchange office'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function list(Request $request): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);
            $responseDto = $this->campaignService->getAllMarketingCampaignsForExchangeOffice($currentUser);

            return new JsonResponse(
                $responseDto->toArray(),
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

    #[Route('/status', name: 'update_status', methods: ['PATCH'])]
    #[OA\Patch(
        path: '/api/marketing-campaigns/status',
        summary: 'Update campaign status',
        description: 'Update the status of a marketing campaign. Agents can only update campaigns from their own exchange office.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'campaignId',
                in: 'query',
                required: true,
                description: 'Campaign ID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'active', 'completed', 'cancelled'], description: 'New campaign status', example: 'active')
                ],
                required: ['status']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Campaign status updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Campaign status updated successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                new OA\Property(property: 'title', type: 'string'),
                                new OA\Property(property: 'content', type: 'string'),
                                new OA\Property(property: 'status', type: 'string'),
                                new OA\Property(property: 'createdAt', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'updatedAt', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'exchangeOffice', properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    new OA\Property(property: 'name', type: 'string')
                                ]),
                                new OA\Property(property: 'createdBy', properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    new OA\Property(property: 'firstName', type: 'string'),
                                    new OA\Property(property: 'lastName', type: 'string')
                                ])
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request - Invalid input data'),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid or missing authentication'),
            new OA\Response(response: 403, description: 'Forbidden - Access denied to campaign from different exchange office'),
            new OA\Response(response: 404, description: 'Campaign not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function updateStatus(
        #[MapQueryString] MarketingCampaignQueryDTO $queryDto,
        #[MapRequestPayload] UpdateCampaignStatusRequestDTO $statusDto,
        Request $request,
    ): JsonResponse {
        try {
            $currentUser = $this->authService->getUserFromToken($request);

            $response = $this->campaignService->updateCampaignStatusById($queryDto->campaignId, $statusDto->status, $currentUser);

            return new JsonResponse([
                'message' => 'Campaign status updated successfully',
                'data' => $response
            ], Response::HTTP_OK);
            
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

    #[Route('/add-target-clients', name: 'add_target_clients', methods: ['POST'])]
    #[OA\Post(
        path: '/api/marketing-campaigns/add-target-clients',
        summary: 'Add target clients to a marketing campaign',
        description: 'Add one or more target clients to an existing marketing campaign. Only agents from the same exchange office can modify the campaign.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'campaignId',
                in: 'query',
                required: true,
                description: 'Marketing campaign ID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'clientIds',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'uuid'),
                        description: 'Array of client IDs to add as targets',
                        example: ['123e4567-e89b-12d3-a456-426614174000', '123e4567-e89b-12d3-a456-426614174001']
                    )
                ],
                required: ['clientIds']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Target clients added successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(property: 'startDate', type: 'string', format: 'date'),
                        new OA\Property(property: 'endDate', type: 'string', format: 'date'),
                        new OA\Property(property: 'targetClientsCount', type: 'integer')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error or business logic error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Campaign not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function addTargetClients(
        #[MapQueryString] MarketingCampaignQueryDTO $queryDto,
        #[MapRequestPayload] ManageTargetClientsDTO $dto,
        Request $request
    ): JsonResponse {
        try {
            $currentUser = $this->authService->getUserFromToken($request);
            $campaignDTO = $this->campaignService->addTargetClients($queryDto->campaignId, $dto, $currentUser);
            
            return new JsonResponse($campaignDTO->toArray(), Response::HTTP_OK);
            
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/remove-target-clients', name: 'remove_target_clients', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/marketing-campaigns/remove-target-clients',
        summary: 'Remove target clients from a marketing campaign',
        description: 'Remove one or more target clients from an existing marketing campaign. Only agents from the same exchange office can modify the campaign.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'campaignId',
                in: 'query',
                required: true,
                description: 'Marketing campaign ID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'clientIds',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'uuid'),
                        description: 'Array of client IDs to remove from targets',
                        example: ['123e4567-e89b-12d3-a456-426614174000', '123e4567-e89b-12d3-a456-426614174001']
                    )
                ],
                required: ['clientIds']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Target clients removed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'title', type: 'string'),
                        new OA\Property(property: 'description', type: 'string'),
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(property: 'startDate', type: 'string', format: 'date'),
                        new OA\Property(property: 'endDate', type: 'string', format: 'date'),
                        new OA\Property(property: 'targetClientsCount', type: 'integer')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error or business logic error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Campaign not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string')
                    ]
                )
            )
        ]
    )]
    public function removeTargetClients(
        #[MapQueryString] MarketingCampaignQueryDTO $queryDto,
        #[MapRequestPayload] ManageTargetClientsDTO $dto,
        Request $request
    ): JsonResponse {
        try {
            $currentUser = $this->authService->getUserFromToken($request);
            $campaignDTO = $this->campaignService->removeTargetClients($queryDto->campaignId, $dto, $currentUser);
            
            return new JsonResponse($campaignDTO->toArray(), Response::HTTP_OK);
            
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

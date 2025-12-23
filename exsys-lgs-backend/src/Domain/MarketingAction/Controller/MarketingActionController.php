<?php

namespace App\Domain\MarketingAction\Controller;

use App\Domain\MarketingAction\DTO\MarketingActionCreateDTO;
use App\Domain\MarketingAction\DTO\MarketingActionQueryDTO;
use App\Domain\MarketingAction\DTO\MarketingActionByCampaignQueryDTO;
use App\Domain\MarketingAction\Mapper\MarketingActionMapper;
use App\Domain\MarketingAction\Service\MarketingActionService;
use App\Domain\Auth\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Marketing Actions')]
class MarketingActionController extends AbstractController
{
    private AuthService $authService;

    public function __construct(
        private MarketingActionService $marketingActionService,
        AuthService $authService
    ) {
        $this->authService = $authService;
    }

    #[
        Route('/api/agent/marketing-action', name: 'agent_marketing_action_create', methods: ['POST']),
        OA\Post(
            path: '/api/agent/marketing-action',
            summary: 'Create a marketing action for clients of a marketing campaign (agent)',
            description: 'Allows an agent to create a marketing action linked to an existing marketing campaign. The action will automatically target all clients associated with the specified campaign. Agents can only create actions for campaigns within their exchange office.',
            security: [['Bearer' => []]],
            tags: ['Marketing Actions'],
            requestBody: new OA\RequestBody(
                required: true,
                description: 'Marketing action data to create',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'title', 
                            type: 'string', 
                            description: 'Marketing action title - must be descriptive and unique within the campaign',
                            example: 'Summer Promotion Email'
                        ),
                        new OA\Property(
                            property: 'channelType', 
                            type: 'string', 
                            enum: ['email', 'sms', 'whatsapp'], 
                            description: 'Communication channel type - determines how the message will be delivered to clients',
                            example: 'email'
                        ),
                        new OA\Property(
                            property: 'content', 
                            type: 'string', 
                            description: 'Marketing action content - the actual message that will be sent to targeted clients',
                            example: 'Take advantage of our special summer exchange rates! Limited time offer.'
                        ),
                        new OA\Property(
                            property: 'campaignId', 
                            type: 'string', 
                            format: 'uuid', 
                            description: 'Marketing campaign ID - target clients will be automatically retrieved from the campaign. Agent must have access to this campaign.',
                            example: '550e8400-e29b-41d4-a716-446655440000'
                        ),
                    ],
                    required: ['title', 'channelType', 'content', 'campaignId']
                )
            ),
            responses: [
                new OA\Response(
                    response: 201,
                    description: 'Marketing action created successfully',
                    content: new OA\JsonContent(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', format: 'uuid', description: 'Unique identifier of the created marketing action'),
                            new OA\Property(property: 'message', type: 'string', example: 'Marketing action created successfully', description: 'Success confirmation message')
                        ]
                    )
                ),
                new OA\Response(
                    response: 400,
                    description: 'Validation error or invalid data provided',
                    content: new OA\JsonContent(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'error', type: 'string', example: 'Invalid campaign ID or validation failed', description: 'Error message describing what went wrong'),
                            new OA\Property(property: 'errors', type: 'string', description: 'Detailed validation errors if applicable')
                        ]
                    )
                ),
                new OA\Response(
                    response: 401,
                    description: 'Authentication required - Invalid or missing token'
                ),
                new OA\Response(
                    response: 403,
                    description: 'Access denied - Agent does not have permission to access this campaign'
                )
            ]
        )
    ]
    public function create(
        #[MapRequestPayload] MarketingActionCreateDTO $dto,
        Request $request
    ): JsonResponse {

        $agent = $this->authService->getUserFromToken($request);
        try {
            $responseDTO = $this->marketingActionService->createMarketingActionForAgent($dto, $agent);
            return new JsonResponse($responseDTO->toArray(), Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[
        Route('/api/agent/marketing-action', name: 'agent_marketing_action_get', methods: ['GET']),
        OA\Get(
            path: '/api/agent/marketing-action',
            summary: 'Retrieve a marketing action and selected clients (agent)',
            description: 'Retrieves detailed information about a specific marketing action including its content, campaign details, and the list of targeted clients. Agents can only access actions within their exchange office.',
            security: [['Bearer' => []]],
            tags: ['Marketing Actions'],
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'query',
                    required: true,
                    description: 'Marketing action unique identifier (UUID format) - specifies which marketing action to retrieve',
                    example: '550e8400-e29b-41d4-a716-446655440000',
                    schema: new OA\Schema(type: 'string', format: 'uuid')
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Marketing action and clients retrieved successfully',
                    content: new OA\JsonContent(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', format: 'uuid', description: 'Marketing action unique identifier'),
                            new OA\Property(property: 'title', type: 'string', example: 'Summer Promotion Campaign', description: 'Marketing action title'),
                            new OA\Property(property: 'channelType', type: 'string', enum: ['email', 'sms', 'whatsapp'], example: 'email', description: 'Communication channel used for this action'),
                            new OA\Property(property: 'content', type: 'string', example: 'Special summer rates available now!', description: 'Marketing message content'),
                            new OA\Property(property: 'campaignId', type: 'string', format: 'uuid', description: 'Associated marketing campaign ID'),
                            new OA\Property(property: 'campaignTitle', type: 'string', example: 'Q3 Customer Retention', description: 'Associated marketing campaign title'),
                            new OA\Property(
                                property: 'clients',
                                type: 'array',
                                description: 'List of clients targeted by this marketing action',
                                items: new OA\Items(
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'string', format: 'uuid', description: 'Client unique identifier'),
                                        new OA\Property(property: 'firstName', type: 'string', example: 'John', description: 'Client first name'),
                                        new OA\Property(property: 'lastName', type: 'string', example: 'Doe', description: 'Client last name')
                                    ]
                                )
                            )
                        ]
                    )
                ),
                new OA\Response(
                    response: 400,
                    description: 'Invalid request or marketing action not found',
                    content: new OA\JsonContent(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'error', type: 'string', example: 'Marketing action not found or access denied', description: 'Error message explaining the issue')
                        ]
                    )
                ),
                new OA\Response(
                    response: 401,
                    description: 'Authentication required - Invalid or missing token'
                ),
                new OA\Response(
                    response: 403,
                    description: 'Access denied - Agent does not have permission to access this marketing action'
                )
            ]
        )
    ]
    public function getMarketingAction(
        #[MapQueryString] MarketingActionQueryDTO $queryDto,
        Request $request
    ): JsonResponse {
        $agent = $this->authService->getUserFromToken($request);
  
        try {
            $responseDTO = $this->marketingActionService->getMarketingActionForAgent($queryDto->id, $agent);
            return new JsonResponse($responseDTO->toArray(), Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[
        Route('/api/agent/marketing-actions/by-campaign', name: 'agent_marketing_actions_by_campaign', methods: ['GET']),
        OA\Get(
            path: '/api/agent/marketing-actions/by-campaign',
            summary: 'Retrieve all marketing actions of a specific campaign (agent)',
            description: 'Retrieves all marketing actions associated with a specific marketing campaign, including campaign details and a summary of each action. This endpoint helps agents view the complete marketing activity for a campaign within their exchange office.',
            security: [['Bearer' => []]],
            tags: ['Marketing Actions'],
            parameters: [
                new OA\Parameter(
                    name: 'campaignId',
                    in: 'query',
                    required: true,
                    description: 'Marketing campaign unique identifier (UUID format) - specifies which campaign\'s marketing actions to retrieve',
                    example: '550e8400-e29b-41d4-a716-446655440000',
                    schema: new OA\Schema(type: 'string', format: 'uuid')
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Campaign marketing actions list retrieved successfully',
                    content: new OA\JsonContent(
                        type: 'object',
                        properties: [
                            new OA\Property(
                                property: 'marketingCampaign',
                                type: 'object',
                                description: 'Marketing campaign information',
                                properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid', description: 'Campaign unique identifier'),
                                    new OA\Property(property: 'title', type: 'string', example: 'Q3 Customer Retention', description: 'Campaign title'),
                                    new OA\Property(property: 'description', type: 'string', example: 'Quarterly campaign to retain high-value clients', description: 'Campaign description'),
                                    new OA\Property(property: 'startDate', type: 'string', format: 'datetime', example: '2024-07-01T00:00:00Z', description: 'Campaign start date'),
                                    new OA\Property(property: 'endDate', type: 'string', format: 'datetime', example: '2024-09-30T23:59:59Z', description: 'Campaign end date'),
                                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'active', 'paused', 'completed'], example: 'active', description: 'Current campaign status'),
                                    new OA\Property(property: 'createdAt', type: 'string', format: 'datetime', description: 'Campaign creation timestamp'),
                                    new OA\Property(property: 'updatedAt', type: 'string', format: 'datetime', description: 'Campaign last update timestamp')
                                ]
                            ),
                            new OA\Property(
                                property: 'marketingActions',
                                type: 'array',
                                description: 'List of marketing actions for this campaign',
                                items: new OA\Items(
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'string', format: 'uuid', description: 'Marketing action unique identifier'),
                                        new OA\Property(property: 'title', type: 'string', example: 'Welcome Email', description: 'Marketing action title'),
                                        new OA\Property(property: 'channelType', type: 'string', enum: ['email', 'sms', 'whatsapp'], example: 'email', description: 'Communication channel'),
                                        new OA\Property(property: 'content', type: 'string', example: 'Welcome to our premium service!', description: 'Marketing message content (truncated for list view)'),
                                        new OA\Property(property: 'createdAt', type: 'string', format: 'datetime', description: 'Action creation timestamp')
                                    ]
                                )
                            ),
                            new OA\Property(property: 'total', type: 'integer', example: 5, description: 'Total number of marketing actions in this campaign')
                        ]
                    )
                ),
                new OA\Response(
                    response: 400,
                    description: 'Invalid campaign ID or access denied',
                    content: new OA\JsonContent(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'error', type: 'string', example: 'Campaign not found or access denied', description: 'Error message explaining the issue')
                        ]
                    )
                ),
                new OA\Response(
                    response: 401,
                    description: 'Authentication required - Invalid or missing token'
                ),
                new OA\Response(
                    response: 403,
                    description: 'Access denied - Agent does not have permission to access this campaign'
                )
            ]
        )
    ]
    public function listMarketingActionsByCampaign(
        #[MapQueryString] MarketingActionByCampaignQueryDTO $queryDto,
        Request $request
    ): JsonResponse {
        $agent = $this->authService->getUserFromToken($request);
        try {
            $responseDTO = $this->marketingActionService->getMarketingActionsByCampaign($queryDto->campaignId, $agent);
            return new JsonResponse($responseDTO->toArray(), Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}

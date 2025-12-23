<?php

namespace App\Domain\QuickMessage\Controller;

use App\Domain\QuickMessage\DTO\QuickMessageCreateDTO;
use App\Domain\QuickMessage\DTO\QuickMessageQueryDTO;
use App\Domain\QuickMessage\Mapper\QuickMessageMapper;
use App\Domain\QuickMessage\Service\QuickMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Domain\Auth\Service\AuthService;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Quick Messages')]
class QuickMessageController extends AbstractController
{
    private AuthService $authService;

    public function __construct(private QuickMessageService $quickMessageService, AuthService $authService) {
        $this->authService = $authService;
    }

    #[
        Route('/api/agent/quick-message', name: 'agent_quick_message_create', methods: ['POST']),
        OA\Post(
            path: '/api/agent/quick-message',
            summary: 'Create a quick message for exchange office clients (agent)',
            description: 'Allows an agent to create a quick message targeting specific clients within their exchange office. The agent selects individual clients and sends them a personalized message through the chosen communication channel.',
            security: [['Bearer' => []]],
            tags: ['Quick Messages'],
            requestBody: new OA\RequestBody(
                required: true,
                description: 'Quick message data to create',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'title', 
                            type: 'string', 
                            description: 'Quick message title - should be descriptive and concise',
                            example: 'Urgent: New Exchange Rates'
                        ),
                        new OA\Property(
                            property: 'channelType', 
                            type: 'string', 
                            enum: ['email', 'sms', 'whatsapp'], 
                            description: 'Communication channel type for message delivery',
                            example: 'email'
                        ),
                        new OA\Property(
                            property: 'content', 
                            type: 'string', 
                            description: 'Quick message content - the actual message to be sent to clients',
                            example: 'We have updated our exchange rates. Check our new competitive rates!'
                        ),
                        new OA\Property(
                            property: 'targets',
                            type: 'array',
                            description: 'List of target clients for this quick message',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(
                                        property: 'clientId', 
                                        type: 'string', 
                                        format: 'uuid', 
                                        description: 'Target client unique identifier',
                                        example: '550e8400-e29b-41d4-a716-446655440000'
                                    )
                                ]
                            )
                        )
                    ],
                    required: ['title', 'channelType', 'content', 'targets']
                )
            ),
            responses: [
                new OA\Response(
                    response: 201,
                    description: 'Quick message created successfully',
                    content: new OA\JsonContent(
                        type: 'object',
                        properties: [
                            new OA\Property(
                                property: 'id', 
                                type: 'string', 
                                format: 'uuid', 
                                description: 'Unique identifier of the created quick message',
                                example: '550e8400-e29b-41d4-a716-446655440000'
                            ),
                            new OA\Property(
                                property: 'message', 
                                type: 'string', 
                                description: 'Success confirmation message',
                                example: 'Quick message created successfully'
                            )
                        ]
                    )
                ),
                new OA\Response(
                    response: 400,
                    description: 'Validation error or invalid data provided'
                ),
                new OA\Response(
                    response: 401,
                    description: 'Authentication required - Invalid or missing token'
                ),
                new OA\Response(
                    response: 403,
                    description: 'Access denied - Agent does not have permission to access specified clients'
                )
            ]
        )
    ]
    public function create(
        #[MapRequestPayload] QuickMessageCreateDTO $dto,
        Request $request
    ): JsonResponse {
        $agent = $this->authService->getUserFromToken($request);;
        try {
            $responseDTO = $this->quickMessageService->createQuickMessageForAgent($dto, $agent);
            return new JsonResponse($responseDTO->toArray(), Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[
        Route('/api/agent/quick-message', name: 'agent_quick_message_get', methods: ['GET']),
        OA\Get(
            path: '/api/agent/quick-message',
            summary: 'Retrieve a quick message and selected clients (agent)',
            description: 'Retrieves detailed information about a specific quick message including its content and the list of targeted clients. Agents can only access quick messages within their exchange office.',
            security: [['Bearer' => []]],
            tags: ['Quick Messages'],
            parameters: [
                new OA\Parameter(
                    name: 'id',
                    in: 'query',
                    required: true,
                    description: 'Quick message unique identifier (UUID format)',
                    example: '550e8400-e29b-41d4-a716-446655440000',
                    schema: new OA\Schema(type: 'string', format: 'uuid')
                )
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Quick message and clients retrieved successfully',
                    content: new OA\JsonContent(
                        type: 'object',
                        properties: [
                            new OA\Property(
                                property: 'id', 
                                type: 'string', 
                                format: 'uuid', 
                                description: 'Quick message unique identifier',
                                example: '550e8400-e29b-41d4-a716-446655440000'
                            ),
                            new OA\Property(
                                property: 'title', 
                                type: 'string', 
                                description: 'Quick message title',
                                example: 'Urgent: New Exchange Rates'
                            ),
                            new OA\Property(
                                property: 'channelType', 
                                type: 'string', 
                                enum: ['email', 'sms', 'whatsapp'], 
                                description: 'Communication channel used for this message',
                                example: 'email'
                            ),
                            new OA\Property(
                                property: 'content', 
                                type: 'string', 
                                description: 'Quick message content',
                                example: 'We have updated our exchange rates. Check our new competitive rates!'
                            ),
                            new OA\Property(
                                property: 'clients',
                                type: 'array',
                                description: 'List of clients targeted by this quick message',
                                items: new OA\Items(
                                    type: 'object',
                                    properties: [
                                        new OA\Property(
                                            property: 'id', 
                                            type: 'string', 
                                            format: 'uuid', 
                                            description: 'Client unique identifier',
                                            example: '550e8400-e29b-41d4-a716-446655440001'
                                        ),
                                        new OA\Property(
                                            property: 'firstName', 
                                            type: 'string', 
                                            description: 'Client first name',
                                            example: 'John'
                                        ),
                                        new OA\Property(
                                            property: 'lastName', 
                                            type: 'string', 
                                            description: 'Client last name',
                                            example: 'Doe'
                                        )
                                    ]
                                )
                            )
                        ]
                    )
                ),
                new OA\Response(
                    response: 400,
                    description: 'Invalid request or quick message not found'
                ),
                new OA\Response(
                    response: 401,
                    description: 'Authentication required - Invalid or missing token'
                ),
                new OA\Response(
                    response: 403,
                    description: 'Access denied - Agent does not have permission to access this quick message'
                )
            ]
        )
    ]
    public function getQuickMessage(
        #[MapQueryString] QuickMessageQueryDTO $queryDto,
        Request $request
    ): JsonResponse {
        $agent = $this->authService->getUserFromToken($request);
        try {
            $responseDTO = $this->quickMessageService->getQuickMessageForAgent($queryDto->id, $agent);
            return new JsonResponse($responseDTO->toArray(), Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }



    

    #[
        Route('/api/agent/quick-messages', name: 'agent_quick_messages_list', methods: ['GET']),
        OA\Get(
            path: '/api/agent/quick-messages',
            summary: 'Retrieve all quick messages from an exchange office (agent)',
            description: 'Retrieves a list of all quick messages created within the agent\'s exchange office. This provides an overview of all quick message activities with summary information including client count and creation dates.',
            security: [['Bearer' => []]],
            tags: ['Quick Messages'],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Quick messages list retrieved successfully',
                    content: new OA\JsonContent(
                        type: 'object',
                        properties: [
                            new OA\Property(
                                property: 'quickMessages',
                                type: 'array',
                                description: 'List of quick messages in the exchange office',
                                items: new OA\Items(
                                    type: 'object',
                                    properties: [
                                        new OA\Property(
                                            property: 'id', 
                                            type: 'string', 
                                            format: 'uuid', 
                                            description: 'Quick message unique identifier',
                                            example: '550e8400-e29b-41d4-a716-446655440000'
                                        ),
                                        new OA\Property(
                                            property: 'title', 
                                            type: 'string', 
                                            description: 'Quick message title',
                                            example: 'Urgent: New Exchange Rates'
                                        ),
                                        new OA\Property(
                                            property: 'channelType', 
                                            type: 'string', 
                                            enum: ['email', 'sms', 'whatsapp'], 
                                            description: 'Communication channel used',
                                            example: 'email'
                                        ),
                                        new OA\Property(
                                            property: 'content', 
                                            type: 'string', 
                                            description: 'Quick message content (may be truncated for list view)',
                                            example: 'We have updated our exchange rates...'
                                        ),
                                        new OA\Property(
                                            property: 'clientCount', 
                                            type: 'integer', 
                                            description: 'Number of clients targeted by this message',
                                            example: 15
                                        ),
                                        new OA\Property(
                                            property: 'createdAt', 
                                            type: 'string', 
                                            format: 'datetime', 
                                            description: 'Message creation timestamp',
                                            example: '2024-08-06T10:30:00Z'
                                        )
                                    ]
                                )
                            ),
                            new OA\Property(
                                property: 'total', 
                                type: 'integer', 
                                description: 'Total number of quick messages in the exchange office',
                                example: 25
                            )
                        ]
                    )
                ),
                new OA\Response(
                    response: 400,
                    description: 'Error or access denied'
                ),
                new OA\Response(
                    response: 401,
                    description: 'Authentication required - Invalid or missing token'
                ),
                new OA\Response(
                    response: 403,
                    description: 'Access denied - Agent does not have permission to access this exchange office'
                )
            ]
        )
    ]
    public function listQuickMessages(Request $request): JsonResponse
    {
        $agent = $this->authService->getUserFromToken($request);
        try {
            $responseDTO = $this->quickMessageService->getAllQuickMessagesForAgentExchangeOffice($agent);
            return new JsonResponse($responseDTO->toArray(), Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}

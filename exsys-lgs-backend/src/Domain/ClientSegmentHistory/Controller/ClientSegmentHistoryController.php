<?php

namespace App\Domain\ClientSegmentHistory\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\Domain\ClientSegmentHistory\DTO\ClientSegmentHistoryQueryDTO;
use App\Domain\ClientSegmentHistory\Service\ClientSegmentHistoryService;
use App\Domain\ClientSegmentHistory\Mapper\ClientSegmentHistoryResponseMapper;
use App\Shared\Service\ErrorCodeResolver;
use App\Domain\Auth\Service\AuthService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\ClientSegmentHistory\DTO\ClientSegmentHistoryListResponseDTO;

#[Route('/api/clients/segment-history', name: 'api_clients_history_')]
class ClientSegmentHistoryController extends AbstractController
{
    private ClientSegmentHistoryService $historyService;
    private ClientSegmentHistoryResponseMapper $responseMapper;
    private AuthService $authService;
    private ErrorCodeResolver $errorCodeResolver;

    public function __construct(
        ClientSegmentHistoryService $historyService,
        ClientSegmentHistoryResponseMapper $responseMapper,
        AuthService $authService,
        ErrorCodeResolver $errorCodeResolver
    ) {
        $this->historyService = $historyService;
        $this->responseMapper = $responseMapper;
        $this->authService = $authService;
        $this->errorCodeResolver = $errorCodeResolver;
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/clients/segment-history',
        summary: 'Get client segment history',
        description: 'Retrieve segment history for a client. Admins can view history for any client, agents can only view history for clients in their exchange office.',
        security: [['Bearer' => []]],
        parameters: [
            new OA\Parameter(
                name: 'clientId',
                in: 'query',
                required: true,
                description: 'Client UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            )
        ],
        tags: ['Clients'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Client segment history retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'message' => new OA\Property(property: 'message', type: 'string', example: 'Client segment history retrieved successfully'),
                        'history' => new OA\Property(
                            property: 'history',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    'segment' => new OA\Property(property: 'segment', type: 'string'),
                                    'createdAt' => new OA\Property(property: 'createdAt', type: 'string', format: 'date-time')
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Bad request - Client ID is required'),
            new OA\Response(response: 401, description: 'Unauthorized - Invalid token'),
            new OA\Response(response: 403, description: 'Forbidden - Not allowed to view this client history'),
            new OA\Response(response: 404, description: 'Not found - Client not found'),
            new OA\Response(response: 500, description: 'Internal server error')
        ]
    )]
    public function list(
        #[MapQueryString] ClientSegmentHistoryQueryDTO $queryDto,
        Request $request
    ): JsonResponse
    {
        try {
            $currentUser = $this->authService->getUserFromToken($request);
            $clientId = $queryDto->clientId;
            $responseDto = $this->historyService->getSegmentHistory($currentUser, $clientId);
            return new JsonResponse($responseDto->toArray(), Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            $statusCode = $this->errorCodeResolver->getStatusCode($e->getMessage());
            return new JsonResponse([
                'error' => $e->getMessage()
            ], $statusCode);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

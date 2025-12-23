<?php

namespace App\Shared\Controller;

use App\Shared\Service\TransactionDetailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/shared/transactions', name: 'api_shared_transactions_')]
class TransactionDetailController extends AbstractController
{
    public function __construct(
        private TransactionDetailService $transactionDetailService
    ) {}

    #[Route('/details', name: 'get_all_details', methods: ['GET'])]
    #[OA\Get(
        path: '/api/shared/transactions/details',
        summary: 'Get all transaction details',
        description: 'Retrieve all transactions with complete details including client and bureau information.',
        security: [['Bearer' => []]],
        tags: ['Shared Transaction Details'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Transaction details retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'success' => new OA\Property(property: 'success', type: 'boolean', example: true),
                        'data' => new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'TransactionID' => new OA\Property(property: 'TransactionID', type: 'string'),
                                    'ClientID' => new OA\Property(property: 'ClientID', type: 'string'),
                                    'BureauID' => new OA\Property(property: 'BureauID', type: 'string'),
                                    'CurrencyFrom' => new OA\Property(property: 'CurrencyFrom', type: 'string'),
                                    'CurrencyTo' => new OA\Property(property: 'CurrencyTo', type: 'string'),
                                    'Amount' => new OA\Property(property: 'Amount', type: 'string'),
                                    'Date' => new OA\Property(property: 'Date', type: 'string'),
                                    'FirstName' => new OA\Property(property: 'FirstName', type: 'string'),
                                    'LastName' => new OA\Property(property: 'LastName', type: 'string'),
                                    'ClientPhone' => new OA\Property(property: 'ClientPhone', type: 'string'),
                                    'ClientEmail' => new OA\Property(property: 'ClientEmail', type: 'string'),
                                    'ClientAddress' => new OA\Property(property: 'ClientAddress', type: 'string'),
                                    'BureauName' => new OA\Property(property: 'BureauName', type: 'string'),
                                    'BureauAddress' => new OA\Property(property: 'BureauAddress', type: 'string'),
                                    'BureauPhone' => new OA\Property(property: 'BureauPhone', type: 'string')
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized - Invalid or missing authentication token'
            ),
            new OA\Response(
                response: 500,
                description: 'Internal server error'
            )
        ]
    )]
    public function getAllTransactionDetails(): JsonResponse
    {
        try {

            // Récupérer les détails des transactions
            $transactionDetails = $this->transactionDetailService->getAllTransactionDetails();

            // Convertir en tableau
            $data = array_map(fn($detail) => $detail->toArray(), $transactionDetails);

            return $this->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'An error occurred while retrieving transaction details',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

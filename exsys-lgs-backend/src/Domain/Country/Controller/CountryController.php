<?php

namespace App\Domain\Country\Controller;

use App\Domain\Country\Service\CountryService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/countries', name: 'api_countries_')]
class CountryController extends AbstractController
{
    public function __construct(
        private CountryService $countryService
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/countries',
        summary: 'Get all countries',
        description: 'Get list of all countries with their nationalities.',
        tags: ['Countries'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Countries retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'countries' => new OA\Property(
                            property: 'countries',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'id' => new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                                    'code' => new OA\Property(property: 'code', type: 'string'),
                                    'name' => new OA\Property(property: 'name', type: 'string'),
                                    'nationality' => new OA\Property(property: 'nationality', type: 'string')
                                ]
                            ),
                            example: [
                                [
                                    'id' => '123e4567-e89b-12d3-a456-426614174000',
                                    'code' => 'FR',
                                    'name' => 'France',
                                    'nationality' => 'French'
                                ]
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getCountries(): JsonResponse
    {
        $countries = $this->countryService->getAllActiveCountries();
        
        return new JsonResponse([
            'countries' => $countries
        ]);
    }
}
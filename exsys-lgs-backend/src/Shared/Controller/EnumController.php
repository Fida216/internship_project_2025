<?php

namespace App\Shared\Controller;

use App\Shared\Enum\Role;
use App\Shared\Enum\Status;
use App\Shared\Enum\Gender;
use App\Shared\Enum\AcquisitionSource;
use App\Shared\Enum\Currency;
use App\Shared\Enum\CampaignStatus;
use App\Shared\Enum\ChannelType;
use App\Domain\Country\Service\CountryService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/enums', name: 'api_enums_')]
class EnumController extends AbstractController
{
    public function __construct(
        private CountryService $countryService
    ) {}

    #[Route('/roles', name: 'roles', methods: ['GET'])]
    #[OA\Get(
        path: '/api/enums/roles',
        summary: 'Get available user roles',
        description: 'Get list of all available user roles.',
        tags: ['Enumerations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Available roles retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'roles' => new OA\Property(
                            property: 'roles',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'admin', 'label' => 'Administrator'],
                                ['value' => 'agent', 'label' => 'Agent']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getRoles(): JsonResponse
    {
        $roles = array_map(function($role) {
            return [
                'value' => $role->value,
                'label' => $role->getLabel()
            ];
        }, Role::cases());
        
        return new JsonResponse([
            'roles' => $roles
        ]);
    }

    #[Route('/statuses', name: 'statuses', methods: ['GET'])]
    #[OA\Get(
        path: '/api/enums/statuses',
        summary: 'Get available statuses',
        description: 'Get list of all available statuses (used for users and exchange offices).',
        tags: ['Enumerations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Available statuses retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'statuses' => new OA\Property(
                            property: 'statuses',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'active', 'label' => 'Active'],
                                ['value' => 'inactive', 'label' => 'Inactive']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getStatuses(): JsonResponse
    {
        $statuses = array_map(function($status) {
            return [
                'value' => $status->value,
                'label' => $status->getLabel()
            ];
        }, Status::cases());
        
        return new JsonResponse([
            'statuses' => $statuses
        ]);
    }

    #[Route('/genders', name: 'genders', methods: ['GET'])]
    #[OA\Get(
        path: '/api/enums/genders',
        summary: 'Get available genders',
        description: 'Get list of all available gender options for clients.',
        tags: ['Enumerations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Available genders retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'genders' => new OA\Property(
                            property: 'genders',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'male', 'label' => 'Male'],
                                ['value' => 'female', 'label' => 'Female']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getGenders(): JsonResponse
    {
        $genders = array_map(function($gender) {
            return [
                'value' => $gender->value,
                'label' => $gender->getLabel()
            ];
        }, Gender::cases());
        
        return new JsonResponse([
            'genders' => $genders
        ]);
    }

    #[Route('/channel-types', name: 'channel_types', methods: ['GET'])]
    #[OA\Get(
        path: '/api/enums/channel-types',
        summary: 'Get available channel types',
        description: 'Get list of all available communication channel types.',
        tags: ['Enumerations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Available channel types retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'channelTypes' => new OA\Property(
                            property: 'channelTypes',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'email', 'label' => 'Email'],
                                ['value' => 'sms', 'label' => 'SMS'],
                                ['value' => 'whatsapp', 'label' => 'WhatsApp']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getChannelTypes(): JsonResponse
    {
        $channelTypes = array_map(function($channelType) {
            return [
                'value' => $channelType->value,
                'label' => $channelType->getLabel()
            ];
        }, ChannelType::cases());
        
        return new JsonResponse([
            'channelTypes' => $channelTypes
        ]);
    }

  

    #[Route('/acquisition-sources', name: 'acquisition_sources', methods: ['GET'])]
    #[OA\Get(
        path: '/api/enums/acquisition-sources',
        summary: 'Get available acquisition sources',
        description: 'Get list of all available acquisition source options for clients.',
        tags: ['Enumerations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Available acquisition sources retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'acquisitionSources' => new OA\Property(
                            property: 'acquisitionSources',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'referral', 'label' => 'Referral'],
                                ['value' => 'advertising', 'label' => 'Advertising'],
                                ['value' => 'social_media', 'label' => 'Social Media']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getAcquisitionSources(): JsonResponse
    {
        $acquisitionSources = array_map(function($source) {
            return [
                'value' => $source->value,
                'label' => $source->getLabel()
            ];
        }, AcquisitionSource::cases());
        
        return new JsonResponse([
            'acquisitionSources' => $acquisitionSources
        ]);
    }

    #[Route('/currencies', name: 'currencies', methods: ['GET'])]
    #[OA\Get(
        path: '/api/enums/currencies',
        summary: 'Get available currencies',
        description: 'Get list of all available currencies for transactions.',
        tags: ['Enumerations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Available currencies retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'currencies' => new OA\Property(
                            property: 'currencies',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string'),
                                    'symbol' => new OA\Property(property: 'symbol', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'USD', 'label' => 'US Dollar', 'symbol' => '$'],
                                ['value' => 'EUR', 'label' => 'Euro', 'symbol' => '€'],
                                ['value' => 'MAD', 'label' => 'Moroccan Dirham', 'symbol' => 'DH']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getCurrencies(): JsonResponse
    {
        $currencies = array_map(function($currency) {
            return [
                'value' => $currency->value,
                'label' => $currency->getLabel(),
                'symbol' => $currency->getSymbol()
            ];
        }, Currency::cases());
        
        return new JsonResponse([
            'currencies' => $currencies
        ]);
    }

    #[Route('/campaign-statuses', name: 'campaign_statuses', methods: ['GET'])]
    #[OA\Get(
        path: '/api/enums/campaign-statuses',
        summary: 'Get available campaign statuses',
        description: 'Get list of all available marketing campaign statuses.',
        tags: ['Enumerations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Available campaign statuses retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        'campaignStatuses' => new OA\Property(
                            property: 'campaignStatuses',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'draft', 'label' => 'Draft'],
                                ['value' => 'active', 'label' => 'Active'],
                                ['value' => 'completed', 'label' => 'Completed'],
                                ['value' => 'cancelled', 'label' => 'Cancelled']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getCampaignStatuses(): JsonResponse
    {
        $campaignStatuses = array_map(function($status) {
            return [
                'value' => $status->value,
                'label' => $status->getLabel()
            ];
        }, CampaignStatus::cases());
        
        return new JsonResponse([
            'campaignStatuses' => $campaignStatuses
        ]);
    }

    #[Route('/all', name: 'all', methods: ['GET'])]
    #[OA\Get(
        path: '/api/enums/all',
        summary: 'Get all enumerations',
        description: 'Get all available enumerations in a single response for efficient client initialization.',
        tags: ['Enumerations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'All enumerations retrieved successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'roles' => new OA\Property(
                            property: 'roles',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'admin', 'label' => 'Administrator'],
                                ['value' => 'agent', 'label' => 'Agent']
                            ]
                        ),
                        'statuses' => new OA\Property(
                            property: 'statuses',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'active', 'label' => 'Active'],
                                ['value' => 'inactive', 'label' => 'Inactive']
                            ]
                        ),
                        'genders' => new OA\Property(
                            property: 'genders',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'male', 'label' => 'Male'],
                                ['value' => 'female', 'label' => 'Female']
                            ]
                        ),
                        'channelTypes' => new OA\Property(
                            property: 'channelTypes',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'email', 'label' => 'Email'],
                                ['value' => 'sms', 'label' => 'SMS'],
                                ['value' => 'whatsapp', 'label' => 'WhatsApp']
                            ]
                        ),
              
                        'acquisitionSources' => new OA\Property(
                            property: 'acquisitionSources',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    'value' => new OA\Property(property: 'value', type: 'string'),
                                    'label' => new OA\Property(property: 'label', type: 'string')
                                ]
                            ),
                            example: [
                                ['value' => 'referral', 'label' => 'Referral'],
                                ['value' => 'advertising', 'label' => 'Advertising']
                            ]
                        )
                    ]
                )
            )
        ]
    )]
    public function getAllEnums(): JsonResponse
    {
        return new JsonResponse([
            'roles' => array_map(
                fn(Role $role) => ['value' => $role->value, 'label' => $role->getLabel()],
                Role::cases()
            ),
            'statuses' => array_map(
                fn(Status $status) => ['value' => $status->value, 'label' => $status->getLabel()],
                Status::cases()
            ),
            'genders' => array_map(
                fn(Gender $gender) => ['value' => $gender->value, 'label' => $gender->getLabel()],
                Gender::cases()
            ),
            'channelTypes' => array_map(
                fn(ChannelType $channelType) => ['value' => $channelType->value, 'label' => $channelType->getLabel()],
                ChannelType::cases()
            ),
            'acquisitionSources' => array_map(
                fn(AcquisitionSource $source) => ['value' => $source->value, 'label' => $source->getLabel()],
                AcquisitionSource::cases()
            ),
            'currencies' => array_map(
                fn(Currency $currency) => [
                    'value' => $currency->value, 
                    'label' => $currency->getLabel(), 
                    'symbol' => $currency->getSymbol()
                ],
                Currency::cases()
            ),
            'campaignStatuses' => array_map(
                fn(CampaignStatus $status) => ['value' => $status->value, 'label' => $status->getLabel()],
                CampaignStatus::cases()
            )
        ]);
    }
}

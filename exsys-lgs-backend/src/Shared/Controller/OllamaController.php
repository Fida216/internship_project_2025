<?php

namespace App\Shared\Controller;

use App\Shared\DTO\MessageDTO;
use App\Shared\Service\OllamaService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/ollama', name: 'api_ollama_')]
class OllamaController extends AbstractController
{
    public function __construct(
        private OllamaService $ollamaService,
        private ValidatorInterface $validator
    ) {}

    #[Route('/generate-offer-message', name: 'generate_offer_message', methods: ['POST'])]
    #[OA\Post(
        path: '/api/ollama/generate-offer-message',
        summary: 'Générer un message d\'offre pour les clients',
        description: 'Utilise Ollama pour générer un message personnalisé pour informer les clients d\'une nouvelle offre',
        tags: ['Ollama AI'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    'message' => new OA\Property(
                        property: 'message',
                        type: 'string',
                        description: 'L\'idée du message à développer ou message à améliorer',
                        example: 'Informer les clients d\'une promotion spéciale été avec 30% de réduction'
                    ),
                    'language' => new OA\Property(
                        property: 'language',
                        type: 'string',
                        description: 'Langue souhaitée pour le message',
                        enum: ['french', 'english', 'arabic'],
                        example: 'french'
                    )
                ],
                required: ['message', 'language']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Message généré avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'success' => new OA\Property(property: 'success', type: 'boolean', example: true),
                        'generatedMessage' => new OA\Property(
                            property: 'generatedMessage',
                            type: 'string',
                            description: 'Message généré par Ollama',
                            example: 'Cher Monsieur Dupont, nous avons le plaisir de vous informer...'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Données de requête invalides',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'success' => new OA\Property(property: 'success', type: 'boolean', example: false),
                        'errors' => new OA\Property(
                            property: 'errors',
                            type: 'array',
                            items: new OA\Items(type: 'string')
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 503,
                description: 'Service Ollama indisponible',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'success' => new OA\Property(property: 'success', type: 'boolean', example: false),
                        'error' => new OA\Property(property: 'error', type: 'string', example: 'Service Ollama indisponible')
                    ]
                )
            )
        ]
    )]
    public function generateOfferMessage(#[MapRequestPayload] MessageDTO $dto): JsonResponse
    {
        // Vérifier la disponibilité d'Ollama
        if (!$this->ollamaService->isAvailable()) {
            return $this->json([
                'success' => false,
                'error' => 'Service Ollama indisponible'
            ], 503);
        }

        // Générer le message
        $generatedMessage = $this->ollamaService->generateOfferMessage(
            $dto->message,
            $dto->language
        );

        if ($generatedMessage === null) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur lors de la génération du message'
            ], 500);
        }

        return $this->json([
            'success' => true,
            'generatedMessage' => $generatedMessage
        ]);
    }

    #[Route('/improve-message', name: 'improve_message', methods: ['POST'])]
    #[OA\Post(
        path: '/api/ollama/improve-message',
        summary: 'Améliorer un message existant',
        description: 'Utilise Ollama pour refactoriser et améliorer un message créé par un agent',
        tags: ['Ollama AI'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    'message' => new OA\Property(
                        property: 'message',
                        type: 'string',
                        description: 'Message original à améliorer',
                        example: 'Salut, on a une promo super cool pour vous ce mois-ci!'
                    ),
                    'language' => new OA\Property(
                        property: 'language',
                        type: 'string',
                        description: 'Langue souhaitée pour le message amélioré',
                        enum: ['french', 'english', 'arabic'],
                        example: 'french'
                    )
                ],
                required: ['message', 'language']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Message amélioré avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        'success' => new OA\Property(property: 'success', type: 'boolean', example: true),
                        'improvedMessage' => new OA\Property(
                            property: 'improvedMessage',
                            type: 'string',
                            description: 'Message amélioré par Ollama'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Données de requête invalides'
            ),
            new OA\Response(
                response: 503,
                description: 'Service Ollama indisponible'
            )
        ]
    )]
    public function improveMessage(#[MapRequestPayload] MessageDTO $dto): JsonResponse
    {
        // Vérifier la disponibilité d'Ollama
        if (!$this->ollamaService->isAvailable()) {
            return $this->json([
                'success' => false,
                'error' => 'Service Ollama indisponible'
            ], 503);
        }

        // Améliorer le message
        $improvedMessage = $this->ollamaService->improveMessage(
            $dto->message,
            $dto->language
        );

        if ($improvedMessage === null) {
            return $this->json([
                'success' => false,
                'error' => 'Erreur lors de l\'amélioration du message'
            ], 500);
        }

        return $this->json([
            'success' => true,
            'improvedMessage' => $improvedMessage
        ]);
    }

}

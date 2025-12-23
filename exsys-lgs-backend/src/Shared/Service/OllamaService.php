<?php

namespace App\Shared\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class OllamaService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $ollamaBaseUrl,
        private string $ollamaDefaultModel
    ) {}

    /**
     * Génère un message/email pour informer les clients d'offres
     */
    public function generateOfferMessage(string $messageIdea, string $language): ?string {
        $prompt = $this->buildOfferPrompt($messageIdea, $language);
        return $this->generateText($prompt);
    }

    /**
     * Refactorise/améliore les messages créés par les agents
     */
    public function improveMessage(
        string $originalMessage,
        string $language = 'french'
    ): ?string {
        $prompt = $this->buildImprovementPrompt($originalMessage, $language);
        
        return $this->generateText($prompt);
    }

    /**
     * Génère du texte avec Ollama
     */
    private function generateText(string $prompt): ?string
    {
        try {
            $response = $this->httpClient->request('POST', $this->ollamaBaseUrl . '/api/generate', [
                'json' => [
                    'model' => $this->ollamaDefaultModel,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.7,
                        'top_p' => 0.9,
                        'num_predict' => 250
                    ]
                ],
                'timeout' => 120
            ]);

            if ($response->getStatusCode() !== 200) {
                $this->logger->error('Erreur HTTP lors de l\'appel à Ollama', [
                    'status_code' => $response->getStatusCode()
                ]);
                return null;
            }

            $data = $response->toArray();
            
            if (!isset($data['response'])) {
                $this->logger->error('Réponse Ollama invalide', ['data' => $data]);
                return null;
            }

            $this->logger->info('Texte généré avec succès par Ollama');
            return trim($data['response']);

        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $this->logger->error('Erreur lors de l\'appel à Ollama', [
                'error' => $e->getMessage(),
                'class' => get_class($e)
            ]);
            return null;
        }
    }

    /**
     * Construit le prompt pour générer un message d'offre
     */
private function buildOfferPrompt(string $messageIdea, string $language): string {
return <<<PROMPT
You are an assistant helping a currency exchange agent write professional and concise messages to send to clients.

Based on an idea provided by the agent, write a short, clear, polite, and professional message, ready to be sent via SMS or email.

Agent's idea: $messageIdea  
Language: $language

ONLY GENERATE the message to be sent — no title, no comment. Respond only with the message ready to be copied.
PROMPT;

}


    /**
     * Construit le prompt pour améliorer un message existant
     */
    private function buildImprovementPrompt(
        string $originalMessage,
        string $language = 'french'
    ): string {
        return "You are a communication expert. Improve the following message to make it more professional, clear, and engaging while keeping the original meaning.

Original message:
\"$originalMessage\"

Target language: $language

Instructions:
- Keep the original meaning and intention
- Improve clarity and comprehension
- Make it more professional and engaging
- Correct grammar and spelling errors
- Improve structure and flow
- Keep a natural and authentic style
- IMPORTANT: Limit the improved message to 3-4 sentences maximum
- Respond in the target language: $language

ONLY GENERATE the message to be sent — no title, no comment. Respond only with the message ready to be copied.";
    }

    /**
     * Vérifie si Ollama est disponible
     */
    public function isAvailable(): bool
    {
        try {
            $response = $this->httpClient->request('GET', $this->ollamaBaseUrl . '/api/tags', [
                'timeout' => 5
            ]);
            
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            $this->logger->warning('Ollama non disponible', ['error' => $e->getMessage()]);
            return false;
        }
    }
}

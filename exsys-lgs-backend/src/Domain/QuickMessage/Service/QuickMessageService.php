<?php

namespace App\Domain\QuickMessage\Service;

use App\Domain\QuickMessage\DTO\QuickMessageCreateDTO;
use App\Domain\QuickMessage\DTO\QuickMessageResponseDTO;
use App\Domain\QuickMessage\DTO\QuickMessageCreateResponseDTO;
use App\Domain\QuickMessage\DTO\QuickMessageListResponseDTO;
use App\Domain\QuickMessage\DTO\QuickMessageListItemResponseDTO;
use App\Domain\QuickMessage\Entity\QuickMessage;
use App\Domain\QuickMessage\Mapper\QuickMessageMapper;
use App\Domain\QuickMessage\Repository\QuickMessageRepository;
use App\Domain\ExchangeOffice\Repository\ExchangeOfficeRepository;
use App\Domain\Client\Repository\ClientRepository;
use App\Shared\Service\CommunicationService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class QuickMessageService
{
    

    public function getQuickMessageForAgent(string $messageId, $agent = null): QuickMessageResponseDTO
    {
        if (!$agent) {
            throw new \InvalidArgumentException('Authenticated agent required');
        }
        $exchangeOffice = $agent->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Agent does not have an exchange office');
        }
        $quickMessage = $this->quickMessageRepository->find($messageId);
        if (!$quickMessage) {
            throw new \InvalidArgumentException('QuickMessage not found');
        }
        if ($quickMessage->getExchangeOffice()->getId() != $exchangeOffice->getId()) {
            throw new \InvalidArgumentException('Access denied: not your exchange office');
        }
        
        return new QuickMessageResponseDTO($quickMessage);
    }
    public function __construct(
        private QuickMessageRepository $quickMessageRepository,
        private ExchangeOfficeRepository $exchangeOfficeRepository,
        private ClientRepository $clientRepository,
        private EntityManagerInterface $em,
        private CommunicationService $communicationService,
        private LoggerInterface $logger
    ) {}

    public function createQuickMessageForAgent(QuickMessageCreateDTO $dto, $agent = null): QuickMessageCreateResponseDTO
    {
        if (!$agent) {
            throw new \InvalidArgumentException('Authenticated agent required');
        }
        $exchangeOffice = $agent->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Agent does not have an exchange office');
        }
        $clientIds = array_map(fn($t) => $t->clientId, $dto->targets);
        $clients = $this->clientRepository->findBy([
            'id' => $clientIds,
            'exchangeOffice' => $exchangeOffice
        ]);
        if (count($clients) !== count($clientIds)) {
            throw new \InvalidArgumentException('One or more clients not found or do not belong to the exchange office');
        }
        $quickMessage = QuickMessageMapper::fromCreateDTO($dto, $exchangeOffice, $clients);
        $quickMessage->setUser($agent);
        $this->em->persist($quickMessage);
        $this->em->flush();

        $statusMessage = 'Quick message created successfully.';
        switch ($quickMessage->getChannelType()->value) {
            case 'email':
                $emailSent = $this->communicationService->sendEmail(
                    $quickMessage->getTitle(),
                    $quickMessage->getContent(),
                    $quickMessage->getTargetClients(),
                    $quickMessage->getId()->toString()
                );
                $statusMessage .= $emailSent ? ' Email sent.' : ' Email failed to send.';
                break;
            case 'sms':
                $this->communicationService->sendSms(
                    $quickMessage->getContent(),
                    $quickMessage->getTargetClients()
                );
                $statusMessage .= ' SMS sent.';
                break;
            case 'whatsapp':
                $this->communicationService->sendWhatsapp(
                    $quickMessage->getContent(),
                    $quickMessage->getTargetClients()
                );
                $statusMessage .= ' WhatsApp sent.';
                break;
        }
        
        return new QuickMessageCreateResponseDTO($quickMessage->getId()->toString(), $statusMessage);

    }

    public function getAllQuickMessagesForAgentExchangeOffice($agent = null): QuickMessageListResponseDTO
    {
        if (!$agent) {
            throw new \InvalidArgumentException('Authenticated agent required');
        }
        $exchangeOffice = $agent->getExchangeOffice();
        if (!$exchangeOffice) {
            throw new \InvalidArgumentException('Agent does not have an exchange office');
        }
        $messages = $this->quickMessageRepository->findBy(['exchangeOffice' => $exchangeOffice]);
        $messageDTOs = [];
        foreach ($messages as $quickMessage) {
            $messageDTOs[] = new QuickMessageListItemResponseDTO($quickMessage);
        }
        return new QuickMessageListResponseDTO($messageDTOs);
    }
}
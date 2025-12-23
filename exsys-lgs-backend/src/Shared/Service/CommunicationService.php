<?php

namespace App\Shared\Service;

use App\Domain\QuickMessage\Entity\QuickMessage;
use App\Domain\QuickMessage\Entity\QuickMessageTargetClient;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class CommunicationService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger
    ) {}

    public function sendEmail(string $title, string $content, Collection $targetClients, ?string $messageId = null): bool
    {
        $recipients = [];
        foreach ($targetClients as $target) {
            $client = $target->getClient();
            if ($client->getEmail()) {
                $recipients[] = $client->getEmail();
            }
        }

        if (empty($recipients)) {
            $this->logger->info('Envoi d\'email annulé : aucun destinataire valide.', ['messageId' => $messageId]);
            return true; 
        }

        $email = (new Email())
            ->from('atest.devz@gmail.com') 
            ->to(...$recipients)
            ->subject($title)
            ->html($content);

        try {
            $this->mailer->send($email);
            $this->logger->info('Email envoyé avec succès.', [
                'messageId' => $messageId,
                'recipients' => $recipients
            ]);
            return true;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Échec de l\'envoi de l\'email.', [
                'messageId' => $messageId,
                'recipients' => $recipients,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function sendSms(string $content, Collection $targetClients): void
    {
        $recipients = [];
        foreach ($targetClients as $target) {
            $client = $target->getClient();
            if ($client->getPhone()) {
                $recipients[] = $client->getPhone();
            }
        }

        if (empty($recipients)) {
           //echo "Envoi de SMS annulé : aucun numéro de téléphone valide.\n";
            return;
        }

        foreach ($recipients as $phoneNumber) {
            //echo "Envoi d'un SMS vers {$phoneNumber}: {$content}\n";
        }
    }

    public function sendWhatsapp(string $content, Collection $targetClients): void
    {
        $recipients = [];
        foreach ($targetClients as $target) {
            $client = $target->getClient();
            if ($client->getWhatsapp()) {
                $recipients[] = $client->getWhatsapp();
            }
        }

        if (empty($recipients)) {
            //echo "Envoi de WhatsApp annulé : aucun numéro de téléphone valide.\n";
            return;
        }

        foreach ($recipients as $phoneNumber) {
            //echo "Envoi d'un WhatsApp vers {$phoneNumber}: {$content}\n";
        }
    }
}

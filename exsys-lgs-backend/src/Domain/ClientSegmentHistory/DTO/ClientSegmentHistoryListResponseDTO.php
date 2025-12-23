<?php

namespace App\Domain\ClientSegmentHistory\DTO;

class ClientSegmentHistoryListResponseDTO
{
    private string $message;
    /** @var array */
    private array $history;

    public function __construct(string $message, array $history)
    {
        $this->message = $message;
        $this->history = $history;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getHistory(): array
    {
        return $this->history;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'history' => $this->history,
        ];
    }
}

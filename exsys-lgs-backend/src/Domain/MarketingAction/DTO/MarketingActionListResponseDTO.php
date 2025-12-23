<?php

namespace App\Domain\MarketingAction\DTO;

class MarketingActionListResponseDTO
{
    /** @var MarketingActionListItemResponseDTO[] */
    public array $marketingActions;
    public int $total;

    public function __construct(array $marketingActions)
    {
        $this->marketingActions = $marketingActions;
        $this->total = count($marketingActions);
    }

    public function toArray(): array
    {
        return [
            'marketingActions' => array_map(fn($action) => $action->toArray(), $this->marketingActions),
            'total' => $this->total,
        ];
    }
}

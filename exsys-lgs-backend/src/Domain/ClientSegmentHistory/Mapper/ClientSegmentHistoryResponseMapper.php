<?php

namespace App\Domain\ClientSegmentHistory\Mapper;

use App\Domain\ClientSegmentHistory\Entity\ClientSegmentHistory;

class ClientSegmentHistoryResponseMapper
{
    public function map(ClientSegmentHistory $history): array
    {
        return [
            'id' => $history->getId()->toString(),
            'segment' => $history->getSegment(),
            'createdAt' => $history->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Map an array of history entries
     *
     * @param ClientSegmentHistory[] $histories
     * @return array
     */
    public function mapList(array $histories): array
    {
        return array_map([$this, 'map'], $histories);
    }
}

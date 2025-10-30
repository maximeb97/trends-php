<?php

namespace Maximeb97\GoogleTrends\Types;

class RelatedQuery
{
    public string $query;
    public int $value;
    public string $formattedValue;
    public bool $hasData;
    public string $link;

    public function __construct(array $data = [])
    {
        $this->query = $data['query'] ?? '';
        $this->value = $data['value'] ?? 0;
        $this->formattedValue = $data['formattedValue'] ?? '';
        $this->hasData = $data['hasData'] ?? false;
        $this->link = $data['link'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'query' => $this->query,
            'value' => $this->value,
            'formattedValue' => $this->formattedValue,
            'hasData' => $this->hasData,
            'link' => $this->link,
        ];
    }
}

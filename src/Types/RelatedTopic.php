<?php

namespace Maximeb97\GoogleTrends\Types;

class RelatedTopic
{
    public array $topic;
    public int $value;
    public string $formattedValue;
    public bool $hasData;
    public string $link;

    public function __construct(array $data = [])
    {
        $this->topic = $data['topic'] ?? ['mid' => '', 'title' => '', 'type' => ''];
        $this->value = $data['value'] ?? 0;
        $this->formattedValue = $data['formattedValue'] ?? '';
        $this->hasData = $data['hasData'] ?? false;
        $this->link = $data['link'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'topic' => $this->topic,
            'value' => $this->value,
            'formattedValue' => $this->formattedValue,
            'hasData' => $this->hasData,
            'link' => $this->link,
        ];
    }
}

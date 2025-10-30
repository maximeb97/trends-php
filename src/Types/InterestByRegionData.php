<?php

namespace Maximeb97\GoogleTrends\Types;

class InterestByRegionData
{
    public string $geoCode;
    public string $geoName;
    public array $value;
    public array $formattedValue;
    public int $maxValueIndex;
    public array $hasData;
    public ?array $coordinates = null;

    public function __construct(array $data = [])
    {
        $this->geoCode = $data['geoCode'] ?? '';
        $this->geoName = $data['geoName'] ?? '';
        $this->value = $data['value'] ?? [];
        $this->formattedValue = $data['formattedValue'] ?? [];
        $this->maxValueIndex = $data['maxValueIndex'] ?? 0;
        $this->hasData = $data['hasData'] ?? [];
        $this->coordinates = $data['coordinates'] ?? null;
    }

    public function toArray(): array
    {
        $result = [
            'geoCode' => $this->geoCode,
            'geoName' => $this->geoName,
            'value' => $this->value,
            'formattedValue' => $this->formattedValue,
            'maxValueIndex' => $this->maxValueIndex,
            'hasData' => $this->hasData,
        ];

        if ($this->coordinates !== null) {
            $result['coordinates'] = $this->coordinates;
        }

        return $result;
    }
}

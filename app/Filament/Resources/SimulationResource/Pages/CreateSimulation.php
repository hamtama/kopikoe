<?php

namespace App\Filament\Resources\SimulationResource\Pages;

use App\Filament\Resources\SimulationResource;
use App\Models\Parameters;
use Filament\Resources\Pages\CreateRecord;

class CreateSimulation extends CreateRecord
{
    protected static string $resource = SimulationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $inputData = [];
        $inputData['name'] = $data['name'] ?? ('Pengujian ' . date('d/m/Y H:i'));
        $inputData['jenis_kopi'] = $data['jenis_kopi'] ?? null;

        $parameters = Parameters::all();
        foreach ($parameters as $param) {
            $code = $param->kode;
            if (isset($data[$code])) {
                $inputData[$code] = $data[$code];
            }
        }

        $normalizedData = SimulationResource::normalizeData($inputData);
        $recommendations = SimulationResource::calculateKNN($normalizedData);

        return [
            'input_data' => $inputData,
            'normalized_data' => $normalizedData,
            'recommendations' => $recommendations,
        ];
    }
}

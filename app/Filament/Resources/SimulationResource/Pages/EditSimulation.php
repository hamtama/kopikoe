<?php

namespace App\Filament\Resources\SimulationResource\Pages;

use App\Filament\Resources\SimulationResource;
use App\Models\Parameters;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimulation extends EditRecord
{
    protected static string $resource = SimulationResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $input = $data['input_data'] ?? [];
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            $input = is_array($decoded) ? $decoded : [];
        }

        if (is_array($input)) {
            $data['name'] = $input['name'] ?? ('Simulasi #' . ($data['id'] ?? ''));
            $data['jenis_kopi'] = $input['jenis_kopi'] ?? null;

            $parameters = Parameters::all();
            foreach ($parameters as $param) {
                $code = $param->kode;
                if (isset($input[$code])) {
                    $data[$code] = $input[$code];
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $inputData = [];
        $inputData['name'] = $data['name'] ?? 'Skenario Uji';
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

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

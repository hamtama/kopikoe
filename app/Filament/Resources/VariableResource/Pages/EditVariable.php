<?php

namespace App\Filament\Resources\VariableResource\Pages;

use App\Filament\Resources\VariableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Variables;
use App\Models\Parameters;

class EditVariable extends EditRecord
{
    protected static string $resource = VariableResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['parameters']) && is_string($data['parameters'])) {
            $data['parameters'] = json_decode($data['parameters'], true) ?? [];
        }

        if (!empty($data['parameters']) && is_array($data['parameters'])) {
            $parameters = Parameters::all();
            $normalized = [];
            foreach ($parameters as $param) {
                $code = $param->kode;
                $val = $data['parameters'][$code]
                    ?? $data['parameters'][strtolower($code)]
                    ?? $data['parameters'][$param->parameter_id]
                    ?? $data['parameters'][str_replace(' ', '_', strtolower(trim($param->name)))]
                    ?? null;

                if ($val !== null) {
                    $normalized[$code] = $val;
                }
            }
            $data['parameters'] = array_merge($data['parameters'], $normalized);
        }

        return $data;
    }

    protected function mutateFormDataBeforeUpdate(array $data): array
    {
        if (isset($data['parameters']) && is_array($data['parameters'])) {
            $parameters = [];
            foreach ($data['parameters'] as $key => $value) {
                $parameters[$key] = $value;
            }
            $data['parameters'] = $parameters;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

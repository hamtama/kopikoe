<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\SimulationResource;

class Simulation extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'input_data',
        'normalized_data',
        'recommendations',
    ];

    protected $casts = [
        'input_data' => 'array',
        'normalized_data' => 'array',
        'recommendations' => 'array',
    ];

    public function getInputDataAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function getNormalizedDataAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function getRecommendationsAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($simulation) {
            $input = $simulation->input_data;
            if (is_string($input)) {
                $input = json_decode($input, true);
                if (is_string($input)) {
                    $input = json_decode($input, true);
                }
            }

            if (is_array($input) && !empty($input)) {
                $normalizedData = SimulationResource::normalizeData($input);
                $recommendations = SimulationResource::calculateKNN($normalizedData);

                $simulation->input_data = $input;
                $simulation->normalized_data = $normalizedData;
                $simulation->recommendations = $recommendations;
            }
        });
    }
}

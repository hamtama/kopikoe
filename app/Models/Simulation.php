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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($simulation) {
            if (is_array($simulation->input_data)) {
                $normalizedData = SimulationResource::normalizeData($simulation->input_data);
                $recommendations = SimulationResource::calculateKNN($normalizedData);

                $simulation->normalized_data = $normalizedData;
                $simulation->recommendations = $recommendations;
            }
        });
    }
}

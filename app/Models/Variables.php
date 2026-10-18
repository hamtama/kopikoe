<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variables extends Model
{
    use HasFactory;
    protected $table = 'variables';
    protected $primaryKey = 'variable_id';

    protected $fillable = [
        'name',
        'nama_supplier',
        'alamat',
        'jenis_kopi',
        'parameters',
        'normalisasi',
        'status',
        'image',
    ];

    protected $casts = [
        'parameters' => 'array',
        'normalisasi' => 'array',
        'image' => 'array'
    ];

    public function JenisKopi()
    {
        return $this->belongsTo(JenisKopi::class, 'jenis_kopi', 'id_jenis_kopi');
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            if (is_string($model->parameters)) {
                $model->parameters = json_decode($model->parameters, true);
            }

            // Normalisasi data sebelum menyimpan jika belum terisi
            if (empty($model->normalisasi) && !empty($model->parameters)) {
                $model->normalisasi = self::normalizeData($model->parameters);
            }
            $model->status = 1; // Set status aktif
        });
    }

    public function isValid(): bool
    {
        $parameters = Parameters::with('subParameters')->get();

        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            if (!$parameter->is_multiple) {
                if (!isset($this->parameters[$transformedName]) || empty($this->parameters[$transformedName])) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function normalizeData($parameters): array
    {
        $normalized = [];
        $allParams = Parameters::with('subParameters')->get();

        foreach ($allParams as $parameter) {
            $key = $parameter->kode;
            $value = $parameters[$key] ?? null;

            if ($parameter->is_multiple) {
                $subParameters = $parameter->subParameters->pluck('subparameter_id')->toArray();
                $selected = is_array($value) ? $value : [];

                foreach ($subParameters as $subParameter) {
                    $normalized["{$key}_{$subParameter}"] = in_array($subParameter, $selected) ? 1.0 : 0.0;
                }
            } else {
                $min = (float) ($parameter->min ?? 1);
                $max = (float) ($parameter->max ?? 15);
                $numVal = (float) ($value ?? $min);

                // Pencegahan Division by Zero
                $range = ($max - $min) != 0 ? ($max - $min) : 1.0;

                if (strtolower($parameter->type) === 'benefit') {
                    $normalized[$key] = round(($numVal - $min) / $range, 4);
                } else {
                    $normalized[$key] = round(($max - $numVal) / $range, 4);
                }
            }
        }

        return $normalized;
    }
}
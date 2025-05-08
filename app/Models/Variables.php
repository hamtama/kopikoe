<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Variables extends Model
{
    use HasFactory;
    protected $table = 'variables';
    protected $primaryKey = 'variable_id';

    protected $fillable = ['name', 'parameters', 'normalisasi', 'status', 'image', 'jenis_kopi', 'nama_supplier', 'alamat'];

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

            // Normalisasi data sebelum menyimpan
            $model->normalisasi = self::normalizeData($model->parameters);
            $model->status = 1; // Set status to 1 after normalizing
        });
    }

    public function isValid(): bool
    {
        $parameters = Parameters::with('subParameters')->get();

        foreach ($parameters as $parameter) {
            $transformedName = $parameter->kode;

            // Validasi parameter non-multiple
            if (!$parameter->is_multiple) {
                if (!isset($this->parameters[$transformedName]) || empty($this->parameters[$transformedName])) {
                    return false; // Parameter non-multiple belum terisi
                }
            }
        }
        return true; // Semua parameter non-multiple sudah terisi
    }

    public static function normalizeData($parameters): array
    {
        $normalized = [];

        foreach ($parameters as $key => $value) {
            $parameter = Parameters::where('kode', $key)->first();

            if ($parameter) {
                if ($parameter->is_multiple) {
                    $subParameters = SubParameter::where('parameter_id', $parameter->parameter_id)->pluck('subparameter_id')->toArray();
                    foreach ($subParameters as $subParameter) {
                        $normalized["{$key}_{$subParameter}"] = in_array($subParameter, $value) ? 1 : 0;
                    }
                } else {
                    $min = $parameter->min;
                    $max = $parameter->max;
                    if ($parameter->type == 'benefit') {
                        $normalized[$key] = ($value - $min) / ($max - $min);
                    } else {
                        $normalized[$key] = ($max - $value) / ($max - $min);
                    }
                }
            } else {
                $normalized[$key] = 0;
                Log::error("Parameter with key {$key} not found in database.");
            }
        }
        return $normalized;
    }
}

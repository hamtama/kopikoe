<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameters extends Model
{
    use HasFactory;
    protected $primaryKey = 'parameter_id';

    protected $fillable = ['name', 'kode', 'type', 'min', 'max', 'is_multiple'];

    public static function generateKode()
    {
        $prefix = 'K';
        $existingCodes = self::pluck('kode')->map(function ($code) use ($prefix) {
            return (int) str_replace($prefix, '', $code);
        })->toArray();

        sort($existingCodes);

        $nextCode = 1;
        $codeFound = false;
        $missingCodes = [];

        foreach ($existingCodes as $code) {
            while ($code > $nextCode) {
                $missingCodes[] = $prefix . str_pad($nextCode, 2, '0', STR_PAD_LEFT);
                $nextCode++;
            }
            if ($code == $nextCode) {
                $nextCode++;
            }
        }

        // If all codes are sequential without gaps, $nextCode will be the next available code
        if (empty($missingCodes)) {
            $nextCode = $prefix . str_pad(count($existingCodes) + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $nextCode = reset($missingCodes);
        }

        return $nextCode;
    }



    public function subParameters()
    {
        return $this->hasMany(SubParameter::class, 'parameter_id', 'parameter_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubParameter extends Model
{
    use HasFactory;

    protected $primaryKey = 'subparameter_id';

    protected $fillable = [
        'parameter_id',
        'name',
        'bobot'
    ];

    public function parameters()
    {
        return $this->belongsTo(Parameters::class, 'parameter_id', 'parameter_id');
    }
}

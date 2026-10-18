<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKopi extends Model
{
    use HasFactory;

    protected $table = 'jenis_kopi';

    protected $primaryKey = 'id_jenis_kopi';

    protected $fillable = ['jenis_kopi'];

    public function variables()
    {
        return $this->hasMany(Variables::class, 'jenis_kopi', 'id_jenis_kopi');
    }
}

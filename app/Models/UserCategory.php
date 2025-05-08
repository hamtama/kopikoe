<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCategory extends Model
{

    use HasFactory;
    protected $table = 'user_category';
    protected $primaryKey = 'id_ucategory';
    protected $fillable = [
        'name_user_category',

    ];
    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_category_id');
    }
}

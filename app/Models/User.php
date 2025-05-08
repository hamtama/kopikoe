<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'user_category_id',
        'password',
        'is_admin',
        'alamat',
        'no_telepon',
        'tanggal_lahir',
        'social_media',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function canAccessFilament(): bool
    {
        return $this->is_admin;
    }

    public function billingDetails(): HasOne
    {
        return $this->hasOne(BillingDetail::class);
    }

    public function category()
    {
        return $this->belongsTo(UserCategory::class, 'user_category_id');
    }

    // Example of a method to check if the profile is complete
    public function isProfileComplete()
    {
        $requiredFields = ['name', 'alamat', 'tanggal_lahir', 'email', 'no_telepon', 'social_media'];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }
        return true;
    }
}

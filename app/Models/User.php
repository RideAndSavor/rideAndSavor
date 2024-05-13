<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\DB\Core\StringField;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function saveableFields(): array|StringField
    {
        return [
            'name' => StringField::new(),
            'email' => StringField::new(),
            'password' => StringField::new(),
            'phone_no' => StringField::new(),
            'gender' => StringField::new(),
            'phone_no' => StringField::new(),
            'age' => StringField::new(),
            'role' => StringField::new()
        ];
    }

    public function addressess()
    {
        return $this->belongsToMany(Address::class);
    }
}

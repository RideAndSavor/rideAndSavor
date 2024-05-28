<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingPrefix;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, Searchable;

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


    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'email' => StringField::new(),
            'password' => StringField::new(),
            'phone_no' => StringField::new(),
            'gender' => StringField::new(),
            'phone_no' => StringField::new(),
            'age' => StringField::new(),
            'role' => StringField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }


        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }


    public function addresses()
    {
        return $this->belongsToMany(Address::class);
    }

    #[SearchUsingPrefix(['status'])]
    public function toSearchableArray()
    {
        return[
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}

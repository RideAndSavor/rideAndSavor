<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingPrefix;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, Searchable;

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
            'age' => StringField::new(),
            'role' => IntegerField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }


        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roleInfo():BelongsTo{
        return $this->belongsTo(Role::class, 'role');
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

    public function orders():HasMany
    {
        return $this->hasMany(Order::class);

    }
    // In the User model
    public function travels()
    {
        return $this->hasMany(Travel::class, 'user_id'); // 'user_id' is the foreign key in the travels table
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }



    // public function foods()
    // {
    //     return $this->hasManyThrough(Food::class, Order::class, 'user_id', 'id', 'id', 'food_id');
    // }
}

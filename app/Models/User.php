<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Current;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'photo',
        'address',
        'phone_number',
        'role',
        'status',
        'social_id',
        'social_type',
        'currency_id',
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->name
        ];
    }

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

    public function cart()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }

    public function vendor_shop()
    {
        return $this->hasOne(VendorShop::class, 'user_id', 'id');
    }

    public function hasReviewedProduct(int $productId): bool
    {
        return $this->productReviews()->where('product_id', $productId)->exists();
    }

    public function getProductReview(int $productId)
    {
        return $this->productReviews()->where('product_id', $productId)->first();
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function vendor_payouts()
    {
        return $this->hasMany(VendorPayout::class, 'user_id', 'id');
    }

    public static function __set_state(array $array)
    {
        $obj = new self();
        foreach ($array as $key => $value) {
            $obj->$key = $value;
        }
        return $obj;
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}

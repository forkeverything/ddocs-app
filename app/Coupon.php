<?php

namespace App;

use App\Exceptions\CouponAlreadyClaimed;
use App\Exceptions\InvalidCouponCode;
use App\Exceptions\RanOutOfCoupon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Coupon
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $code
 * @property integer $credits
 * @property integer $quantity
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $claimers
 * @method static \Illuminate\Database\Query\Builder|\App\Coupon whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Coupon whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Coupon whereCredits($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Coupon whereQuantity($value)
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'code', 'credits', 'quantity'
    ];

    /**
     * Locate a Coupon model by it's code and throw
     * an error if we can't find it.
     * 
     * @param $code
     * @return mixed
     * @throws InvalidCouponCode
     */
    public static function findByCode($code)
    {
        $coupon = static::where('code', $code)->first();
        if(! $coupon) throw new InvalidCouponCode;
        return $coupon;
    }

    /**
     * The User(s) who have claimed the coupon.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function claimers()
    {
        return $this->belongsToMany(User::class, 'coupon_user', 'user_id', 'coupon_id');
    }

    /**
     * Have a User claim this coupon.
     *
     * @param User $user
     * @return $this
     * @throws CouponAlreadyClaimed
     * @throws RanOutOfCoupon
     */
    public function claim(User $user)
    {
        if ($this->alreadyClaimedBy($user)) throw new CouponAlreadyClaimed;
        if (!$this->quantity > 0) throw new RanOutOfCoupon;

        return $this->giveUserCredits($user)
                    ->addUserToClaimedList($user)
                    ->minusOneQuantity();

    }

    /**
     * Add how ever many credits this Coupon's worth to
     * the User.
     * 
     * @param User $user
     * @return $this
     */
    protected function giveUserCredits(User $user)
    {
        $user->addCredits($this->credits);
        return $this;
    }

    /**
     * Minus quantity by 1.
     *
     * @return $this
     */
    protected function minusOneQuantity()
    {
        $this->quantity = $this->quantity - 1;
        $this->save();
        return $this;
    }


    /**
     * Add User to the list of claimed User(s).
     *
     * @param User $user
     * @return $this
     */
    protected function addUserToClaimedList(User $user)
    {
        $this->claimers()->attach($user);
        return $this;
    }

    /**
     * Check if given user has already claimed this coupon.
     *
     * @param User $user
     * @return mixed
     */
    public function alreadyClaimedBy(User $user)
    {
        return $this->claimers->contains($user);
    }
}

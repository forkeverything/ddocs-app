<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Subscription
 *
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $id
 * @property string $name
 * @property string $stripe_id
 * @property string $stripe_plan
 * @property integer $quantity
 * @property string $trial_ends_at
 * @property string $ends_at
 * @property integer $user_id
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereStripeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereStripePlan($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereEndsAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Subscription whereUserId($value)
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    //
}

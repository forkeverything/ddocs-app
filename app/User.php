<?php

namespace App;

use App\Events\CreatedUserFromEmailWebhook;
use App\Exceptions\NotEnoughCredits;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'credits'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * A User can create many Checklist(s) of required File(s).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }

    /**
     * User can have many File(s) that they may request.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * User can have many Project(s).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }


    /**
     * Minus a User's credit
     *
     * @return $this
     * @throws NotEnoughCredits
     */
    public function minusOneCredit()
    {
        if($this->ranOutOfCredits()) throw new NotEnoughCredits($this);

        $this->update([
            'credits' => $this->credits - 1
        ]);

        return $this;
    }

    /**
     * Check if User has ran out of credits.
     *
     * @return bool
     */
    protected function ranOutOfCredits()
    {
        return $this->credits === 0;
    }

    /**
     * Add credits to User.
     *
     * @param int $numCredits
     * @return $this
     */
    public function addCredits($numCredits)
    {
        $currentCredits = $this->credits;
        $this->update([
            'credits' => ($this->credits + $numCredits)
        ]);
        return $this;
    }

    /**
     * Create a new User from the HTTP Request of PostMark's inbound
     * email webhook.
     *
     * @param Request $request
     * @return static
     */
    public static function makeNewUserFromEmailWebhook(Request $request)
    {
        $randomPassword = str_random(6);

        $user = static::create([
            'name' => $request["FromFull"]["Name"],
            'email' => $request["From"],
            'password' => bcrypt($randomPassword),
            'credits' => 5
        ]);

        Event::fire(new CreatedUserFromEmailWebhook($user, $randomPassword));

        return $user;
    }
}

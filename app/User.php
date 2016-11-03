<?php

namespace App;

use App\Auth\AuthenticatableUser;
use App\Events\CreatedUserFromEmailWebhook;
use App\Exceptions\NotEnoughCredits;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Billable;

/**
 * App\User
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $refresh_token
 * @property integer $refresh_token_expiry
 * @property integer $credits
 * @property string $stripe_id
 * @property string $card_brand
 * @property string $card_last_four
 * @property string $trial_ends_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Checklist[] $checklists
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\File[] $files
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Project[] $projects
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $unreadNotifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Cashier\Subscription[] $subscriptions
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRefreshToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRefreshTokenExpiry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCredits($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereStripeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCardBrand($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCardLastFour($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereTrialEndsAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Project[] $ownProjects
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Project[] $managingProjects
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProjectFile[] $projectFiles
 */
class User extends AuthenticatableUser
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
        'credits',
        'refresh_token',
        'refresh_token_expiry'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'refresh_token',
        'refresh_token_expiry',
        'stripe_id',
        'card_brand',
        'card_last_four',
        'trial_ends_at'
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
     * User can have many Project(s)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    /**
     * ProjectFile(s) that the User has been assigned to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projectFiles()
    {
        return $this->belongsToMany(ProjectFile::class, 'project_file_user', 'user_id', 'project_file_id');
    }

    /**
     * Project(s) that were created by User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownProjects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps()->wherePivot('admin', 1);
    }

    /**
     * Project(s) that are being managed by User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function managingProjects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps()->wherePivot('manager', 1);
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

    /**
     * Add User to Project members and set as admin.
     *
     * @param Project $project
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function startProject(Project $project)
    {
        return $this->projects()->save($project, [
            'admin' => 1
        ]);
    }

    /**
     * Find User by email address.
     *
     * @param $email
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }
}


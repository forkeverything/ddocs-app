<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Billable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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
}

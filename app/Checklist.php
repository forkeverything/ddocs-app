<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    /**
     * Mass-assignable fields for a Checklist
     *
     * @var array
     */
    protected $fillable = [
        'recipient',
        'name',
        'description',
        'user_id'
    ];

    /**
     * A Checklist can have many files that it requires.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Every Checklist was made by a single User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

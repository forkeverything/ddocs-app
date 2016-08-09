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
     * Dynamic props to attach to each Model.
     *
     * @var array
     */
    protected $appends = [
        'hash',
        'progress'
    ];

    /**
     * A Checklist can have many files that it requires.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestedFiles()
    {
        return $this->hasMany(FileRequest::class);
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

    /**
     * Get the hash'd id of this model.
     *
     * @return mixed
     */
    public function getHashAttribute()
    {
        return hashId($this);
    }

    /**
     * Calculate the percentage completion of Checklist using
     * the number of File(s) received.
     *
     * @return float
     */
    public function getProgressAttribute()
    {
        $files = $this->requestedFiles;
        $received = $files->where('status', 'received')->count();
        $total = $files->count();

        return round($received / $total, 0);
    }
}

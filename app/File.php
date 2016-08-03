<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * Assignable fields.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'description',
        'due',
        'required',
        'rejected',
        'checklist_id',
        'version'
    ];

    /**
     * Date fields to be converted automatically via Carbon.
     *
     * @var array
     */
    protected $dates = [
        'due'
    ];

    /**
     * All File(s) belong to a single Checklist that has required them.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    /**
     * Format as Carbon Date only if value given to prevent '0000-00-00 00:00:00'
     *
     * @param $value
     */
    public function setDueAttribute($value)
    {
        if($value) $this->attributes['due'] = Carbon::createFromFormat('d/m/Y', $value);
    }
}

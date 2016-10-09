<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'body',
        'subject_id',
        'subject_type',
        'user_id'
    ];

    protected $with = [
        'sender'
    ];

    /**
     * Create wrapper to add a new comment.
     *
     * @param $subjectId
     * @param $subjectType
     * @param $body
     * @param $userId
     * @return static
     */
    public static function addComment($subjectId, $subjectType, $body, $userId)
    {
        return static::create([
            'subject_id' => $subjectId,
            'subject_type' => $subjectType,
            'body' => $body,
            'user_id' => $userId
        ]);
    }

    /**
     * The subject that this Comment belongs to (FileRequest / ProjectFile)
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * The User model that sent the comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

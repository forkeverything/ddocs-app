<?php

namespace App;

use App\Events\CommentAdded;
use App\Utilities\Traits\Hashable;
use Event;
use Illuminate\Database\Eloquent\Model;
use Log;

/**
 * App\Comment
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $body
 * @property integer $subject_id
 * @property string $subject_type
 * @property integer $user_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 * @property-read \App\User $sender
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereBody($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereSubjectId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereSubjectType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment whereUserId($value)
 * @mixin \Eloquent
 * @property-read mixed $hash
 */
class Comment extends Model
{
    use Hashable;

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
    public static function add($subjectId, $subjectType, $body, $userId)
    {
        $comment = static::create([
            'subject_id' => $subjectId,
            'subject_type' => $subjectType,
            'body' => $body,
            'user_id' => $userId
        ]);

        Event::fire(new CommentAdded($comment));

        return $comment;
    }

    /**
     * Reply via Email
     *
     * @param $originalCommentHash
     * @param $senderEmail
     * @param $commentBody
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function replyByEmail($originalCommentHash, $senderEmail, $commentBody)
    {
        // Sender needs an account
        if(! $senderUser = User::findByEmail($senderEmail)) return response("Didn't post comment. Sender email doesn't have an associated account.");
        // Does original comment exist
        if(! $originalComment = Comment::findByHash($originalCommentHash)) return response("Couldn't find original comment");
        // Parse out comment body from email
        $pattern = "/.*?(?=(?:\s*On.*wrote:|\s*===== Reply above this line =====|$))/s";

        /*
         * Regex Pattern
         * - Match everything until our defualt On...wrote:
         * - Failing to find that, we'll match everything until our reply line
         * - Finally, if both aren't found we'll match everything because we're using
         *   a look-ahead that will match end of line.
         */

        preg_match($pattern, $commentBody, $matches);

        LOG::info($commentBody);

        static::add($originalComment->subject_id, $originalComment->subject_type, $matches[0], $senderUser->id);
        return response("replied to comment");
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

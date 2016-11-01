<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Project
 *
 * @property integer $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $name
 * @property string $description
 * @property integer $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProjectFolder[] $folders
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Project whereUserId($value)
 * @mixin \Eloquent
 */
class Project extends Model
{

    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Members that have been invited but haven't accepted yet.
     *
     * @return mixed
     */
    public function pendingMembers()
    {
        return $this->belongsToMany(User::class)->withTimestamps()
            ->where('accepted', 0);
    }

    /**
     * Members are User(s) that are a part of the Project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class)->withTimestamps()
            ->withPivot('admin', 'manager')
            ->where('accepted', 1);
    }

    /**
     * User that created the Project. Only admins can delete
     * projects and project storage is counted towards
     * admin's quota.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admin()
    {
        return $this->belongsToMany(User::class)->withTimestamps()
            ->wherePivot('admin', 1);
    }

    /**
     * User(s) that have been designated as managers. Managers
     * can add/remove normal members.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function managers()
    {
        return $this->belongsToMany(User::class)->withTimestamps()
            ->wherePivot('manager', 1);
    }

    /**
     * Project can have many different folders.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders()
    {
        return $this->hasMany(ProjectFolder::class, 'project_id');
    }

    /**
     * Mark User as accepted and make the user a member.
     *
     * @param User $user
     * @return mixed
     */
    public function acceptInvitation(User $user)
    {
        return $user->projects()->updateExistingPivot($this->id, ['accepted' => 1]);
    }

    /**
     * Promote or demote a User from Project manager.
     *
     * @param User $user
     * @param $managerStatus
     * @return mixed
     */
    public function defineManager(User $user, $managerStatus)
    {
        return $user->projects()->updateExistingPivot($this->id, ['manager' => $managerStatus]);
    }

    /**
     * Recursively delete each ProjectFolder and subsequent files
     * within the folders before finally deleting
     * the project.
     *
     * @return bool|null
     */
    public function fullDelete()
    {
        foreach ($this->folders as $folder) {
            $folder->fullDelete();
        }
        $this->delete();
        return response("Deleted project");
    }
}

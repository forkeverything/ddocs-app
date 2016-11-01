<?php

namespace App;

use DB;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $pendingMembers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $members
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $admin
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $managers
 */
class Project extends Model
{

    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Members are User(s) that are a part of the Project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class)->withTimestamps()
            ->withPivot('admin', 'manager');
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
     * Add email to project invitations list.
     *
     * @param $email
     * @return bool
     */
    public function createInvitation($email)
    {
        return DB::table('project_invitiation')
          ->insert([
              'email' => $email,
              'project_id' => $this->id
          ]);
    }

    /**
     * Track down invitation by email.
     *
     * @param $email
     * @return bool
     */
    public function findInvitation($email)
    {
        return !! DB::table('project_invitations')
          ->select(DB::raw(1))
          ->where('project_id', $this->id)
          ->where('email', $email)
          ->first();
    }

    /**
     * Whether User's email is on list of project member invitaitons.
     *
     * @param User $user
     * @return bool
     */
    public function hasInvited(User $user)
    {
        return $this->findInvitation($user->email);
    }

    /**
     * Delete invitation by email.
     *
     * @param $email
     * @return int
     */
    public function deleteInvite($email)
    {
        return DB::table('project_invitations')
                 ->where('project_id', $this->id)
                 ->where('email', $email)
                 ->delete();
    }

    /**
     * Save a User as a Project member.
     *
     * @param User $user
     * @return Model
     */
    public function addMember(User $user)
    {
        return $this->members()->save($user);
    }

    /**
     * Remove User from Project.
     *
     * @param User $user
     * @return int
     */
    public function removeMember(User $user)
    {
        return $this->members()->detach($user->id);
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

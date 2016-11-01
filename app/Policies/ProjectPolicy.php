<?php

namespace App\Policies;

use App\Project;
use App\ProjectFile;
use App\ProjectFolder;
use App\User;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    protected function pivotQuery(User $user, Project $project)
    {
        return DB::table('project_user')
                 ->select(DB::raw(1))
                 ->where('project_id', $project->id)
                 ->where('user_id', $user->id);
    }

    /**
     * User is a Project member.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function member(User $user, Project $project)
    {
        return !!$this->pivotQuery($user, $project)
                      ->first();
    }

    /**
     * User is a Project manager.
     *
     * @param User $user
     * @param Project $project
     * @return mixed
     */
    public function manager(User $user, Project $project)
    {
        return !! $this->pivotQuery($user, $project)
                      ->where(function ($query) {
                          $query->where('manager', 1)
                                ->orWhere('admin', 1);
                      })->first();
    }

    /**
     * User is Project admin.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function admin(User $user, Project $project)
    {
        return !!$this->pivotQuery($user, $project)
                      ->where('admin', 1)
                      ->first();
    }

    /**
     * Only allowed to modify ProjectFolder's that belong to
     * the actual Project and User is a member.
     *
     * @param User $user
     * @param Project $project
     * @param ProjectFolder $projectFolder
     * @return bool
     */
    public function updateFolder(User $user, Project $project, ProjectFolder $projectFolder)
    {
        return $this->member($user, $project) && $projectFolder->project_id === $project->id;
    }

    /**
     * ProjectFile's have to be inside a folder that belongs to the Project that
     * the User is a member of.
     *
     * @param User $user
     * @param Project $project
     * @param ProjectFile $projectFile
     * @return bool
     */
    public function updateFile(User $user, Project $project, ProjectFile $projectFile)
    {
        return $this->member($user, $project) && $this->projectFileBelongsToProject($projectFile, $project);
    }

    /**
     * Allowed to add a ProjectFile if we're allowed to make updates to the ProjectFolder
     * and parent Project.
     *
     * @param User $user
     * @param Project $project
     * @param ProjectFolder $projectFolder
     * @return bool
     */
    public function addFile(User $user, Project $project, ProjectFolder $projectFolder)
    {
        return $this->updateFolder($user, $project, $projectFolder);
    }

    /**
     * View a ProjectFile.
     *
     * @param User $user
     * @param Project $project
     * @param ProjectFile $projectFile
     * @return bool
     */
    public function viewFile(User $user, Project $project, ProjectFile $projectFile)
    {
        return $this->member($user, $project) && $this->projectFileBelongsToProject($projectFile, $project);
    }

    /**
     * Checks if ProjectFile belongs to a folder within a Project.
     *
     * @param ProjectFile $projectFile
     * @param Project $project
     * @return bool
     */
    protected function projectFileBelongsToProject(ProjectFile $projectFile, Project $project)
    {
        return $projectFile->folder->project_id === $project->id;
    }
}

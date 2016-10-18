<?php

namespace App\Policies;

use App\Project;
use App\ProjectFile;
use App\ProjectFolder;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether a User can view a given Project.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function view(User $user, Project $project)
    {
        // TODO ::: when we make it you can share projects with team members
        // we need to change this to make sure they're shared.

        return $project->user_id === $user->id;
    }

    /**
     * User can update if the project belongs to them.
     *
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function update(User $user, Project $project)
    {
        // TODO ::: Make updateable by team.

        return $project->user_id === $user->id;
    }

    /**
     * Only allowed to modify ProjectFolder's that belong to
     * the actual Project that User is allowed to update.
     *
     * @param User $user
     * @param Project $project
     * @param ProjectFolder $projectFolder
     * @return bool
     */
    public function updateFolder(User $user, Project $project, ProjectFolder $projectFolder)
    {
        return $this->update($user, $project) &&  $projectFolder->project_id === $project->id;
    }

    /**
     * ProjectFile's have to be inside a folder that belongs to the Project that
     * the User is allowed to update.
     *
     * @param User $user
     * @param Project $project
     * @param ProjectFile $projectFile
     * @return bool
     */
    public function updateFile(User $user, Project $project, ProjectFile $projectFile)
    {
        return $this->update($user, $project) && $this->projectFileBelongsToProject($projectFile, $project);
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
        return $this->view($user, $project) && $this->projectFileBelongsToProject($projectFile, $project);
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

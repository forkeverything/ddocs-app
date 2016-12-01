<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Factories\UploadFactory;
use App\FileRequest;
use App\Http\Requests\AddProjectFileRequest;
use App\Http\Requests\CreateProjectFolderRequest;
use App\Http\Requests\SaveProjectRequest;
use App\Jobs\SendProjectInvite;
use App\Project;
use App\ProjectFile;
use App\ProjectFolder;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;


class ProjectsController extends Controller
{

    /**
     * Return projects for authenticated User.
     *
     * @return mixed
     */
    public function getUserProjects()
    {
        return Auth::user()->projects()->orderBy('name', 'asc')->get();
    }

    /**
     * Handle request to save a new Project.
     *
     * @param SaveProjectRequest $request
     * @return Project
     */
    public function postSaveNew(SaveProjectRequest $request)
    {
        $project = Project::create($request->all());
        Auth::user()->startProject($project);
        return $project;
    }

    /**
     * Single Project in JSON
     *
     * @param Project $project
     * @return Model
     */
    public function getSingleProject(Project $project)
    {
        $this->authorize('member', $project);
        return $project->load([
            'members' => function ($query) {
                $query->orderBy('project_user.admin', 'desc')
                      ->orderBy('project_user.manager', 'desc')
                      ->orderBy('name', 'asc');
            },
            'folders' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'folders.files' => function ($query) {
                $query->with([
                    'fileRequest',
                    'members' => function($query) {
                        $query->orderBy('name', 'asc');
                    }
                ])->orderBy('position', 'asc');
            }
        ]);
    }

    /**
     * Send invite to join Project.
     *
     * @param Project $project
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postSendInvitation(Project $project, Request $request)
    {
        $this->authorize('manager', $project);
        $this->validate($request, [
            'email' => 'required|email',
        ]);
        $email = $request->email;

        $user = User::findByEmail($email);
        if ($user && $project->members->contains($user)) {
            return response([
                'error' => [
                    'Already a member!'
                ]
            ], 422);
        };

        if(!$project->findInvitation($email)) $project->createInvitation($email);
        dispatch(new SendProjectInvite($project, $email, Auth::user()));
        return response("Invitation sent.");
    }

    /**
     * Request to accept an invitation to join Project.
     *
     * @param Project $project
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postJoin(Project $project)
    {
        $user = Auth::user();
        if ($project->members->contains($user)) return response("Already a member.");
        if (!$project->hasInvited($user)) abort(403, "Couldn't find invitation");
        $project->addMember($user);
        $project->deleteInvite($user->email);
        return response("Joined project.");
    }

    /**
     * Handle request to make a User manager or demote from manager.
     *
     * @param Project $project
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postDefineManager(Project $project, Request $request)
    {
        $this->authorize('admin', $project);
        $user = User::findOrFail($request->user_id);
        $project->defineManager($user, $request->manager);
        return response('Defined project manager.');
    }

    /**
     * Removing a member from Project.
     *
     * @param Project $project
     * @param User $user
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteRemoveMember(Project $project, User $user)
    {
        $this->authorize('manager', $project);
        if ($project->hasAdmin($user)) abort(403, "Can't remove admin.");

        // Unassign user from each ProjectFile that belongs to the Project.
        $projectFolderIds = $project->folders->pluck('id');
        $assignedFilesWithinProject = $user->projectFiles()->whereIn('project_folder_id', $projectFolderIds)->get();
        foreach ($assignedFilesWithinProject as $projectFile) {
            $projectFile->unassignMember($user);
        }

        // Remove User as member.
        $project->removeMember($user);
        return response("Removed member");
    }

    /**
     * Single update request to update a Project, ProjectFolder(s) and ProjectFile(s).
     * We batch put updates to increase efficiency and reduce redundant updates as
     * User is most likely performing multiple requests per second.
     *
     * @param Project $project
     * @param Request $request
     * @return Project
     */
    public function putUpdateItems(Project $project, Request $request)
    {
        $this->authorize('member', $project);
        $updatedModels = $request->all();

        if ($updatedProject = $updatedModels['project']) {
            $project->update($updatedProject);
        }

        if ($updatedFolders = $updatedModels['folders']) {
            foreach ($updatedFolders as $id => $updatedFolder) {
                $projectFolder = ProjectFolder::find($id);
                $this->authorize('updateFolder', [$project, $projectFolder]);
                $projectFolder->update($updatedFolder);
            }
        }

        if ($updatedFiles = $updatedModels['files']) {
            foreach ($updatedFiles as $id => $updatedFile) {
                $projectFile = ProjectFile::find($id);
                $this->authorize('updateFile', [$project, $projectFile]);
                $projectFile->update($updatedFile);
            }
        }

        return response('Updated project items.');
    }

    /**
     * Delete Project.
     *
     * @param Project $project
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function delete(Project $project)
    {
        $this->authorize('admin', $project);
        $project->fullDelete();
        return response('Deleted project');
    }

    /**
     * Handle request to create a Project Folder.
     *
     * @param Project $project
     * @param Request $request
     * @return Model
     */
    public function postCreateFolder(Project $project, CreateProjectFolderRequest $request)
    {
        $this->authorize('member', $project);
        return $project->folders()->create($request->all())->load('files');
    }


    /**
     * Delete Project Folder
     *
     * @param Project $project
     * @param ProjectFolder $projectFolder
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function deleteFolder(Project $project, ProjectFolder $projectFolder)
    {
        $this->authorize('updateFolder', [$project, $projectFolder]);
        $projectFolder->fullDelete();
        return response("Deleted project folder");
    }

    /**
     * Create a new File within Project Folder.
     *
     * @param Project $project
     * @param ProjectFolder $projectFolder
     * @param Request $request
     * @return Model
     */
    public function postAddFile(Project $project, ProjectFolder $projectFolder, AddProjectFileRequest $request)
    {
        $this->authorize('addFile', [$project, $projectFolder]);
        $projectFile = $projectFolder->files()->create($request->all());
        return $projectFile->load('fileRequest', 'members');
    }

    public function getProjectFile(Project $project, ProjectFile $projectFile)
    {
        $this->authorize('viewFile', [$project, $projectFile]);
        return $projectFile->loadAllRelations();
    }

    /**
     * Attaches a FileRequest to a ProjectFile.
     *
     * @param Project $project
     * @param ProjectFile $projectFile
     * @param Request $request
     * @return Model
     */
    public function postAttachFileRequest(Project $project, ProjectFile $projectFile, Request $request)
    {
        $this->authorize('updateFile', [$project, $projectFile]);

        if ($fileRequestHash = $request->file_request_hash) {
            // Attaching to a FileRequest
            $fileRequest = FileRequest::findByHash($request->file_request_hash);
            $this->authorize('update', $fileRequest);
            $projectFile->update([
                'file_request_id' => $fileRequest->id
            ]);
        } else {
            // Detaching File Request
            $projectFile->update([
                'file_request_id' => null
            ]);
        }

        return ProjectFile::find($projectFile->id)->fresh()->loadAllRelations();
    }

    /**
     * Directly upload to a ProjectFile.
     *
     * @param Project $project
     * @param ProjectFile $projectFile
     * @param Request $request
     * @return Model
     */
    public function postUploadFile(Project $project, ProjectFile $projectFile, Request $request)
    {
        $this->authorize('updateFile', [$project, $projectFile]);
        $upload = UploadFactory::store($projectFile, $request->file('file'));
        $commentBody = 'Uploaded a <a href="' . awsURL() . $upload->path . '">new file</a>';
        Comment::addComment($projectFile->id, 'App\\ProjectFile', $commentBody, Auth::id());

        // TODO ::: Use pusher so the new comment automatically shows up in thread.

        return $projectFile->loadAllRelations();
    }

    /**
     * Assign/Unassign a member to a ProjectFile.
     *
     * @param Project $project
     * @param ProjectFile $projectFile
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function postSetProjectFileMemberAssignment(Project $project, ProjectFile $projectFile, Request $request)
    {
        $this->authorize('updateFile', [$project, $projectFile]);
        $user = User::find($request->user_id);
        if(! $project->hasMember($user)) abort(400, "Not a member of the project.");
        $request->assign ? $projectFile->assignMember($user) : $projectFile->unassignMember($user);
        return response("Set member assignment for project file.");
    }

    /**
     * Delete Project File.
     *
     * @param Project $project
     * @param ProjectFile $projectFile
     * @return string
     */
    public function deleteProjectFile(Project $project, ProjectFile $projectFile)
    {
        $this->authorize('updateFile', [$project, $projectFile]);
        $projectFile->fullDelete();
        return 'Deleted project file';
    }
}

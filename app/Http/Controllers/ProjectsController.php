<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCommentRequest;
use App\Http\Requests\AddProjectFileRequest;
use App\Http\Requests\CreateProjectFolderRequest;
use App\Http\Requests\SaveProjectRequest;
use App\Project;
use App\ProjectFile;
use App\ProjectFolder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProjectsController extends Controller
{
    public function __construct()
    {

        $this->middleware('can:view,project', [
            'only' => [
                'getSingleProject'
            ]
        ]);

        $this->middleware('can:update,project', [
            'only' => [
                'putUpdateItems',
                'delete',
                'postCreateFolder',
                'deleteFolder',
                'postAddFile',
                'postAddComment'
            ]
        ]);
    }

    /**
     * Return projects for authenticated User.
     *
     * @return mixed
     */
    public function getUserProjects()
    {
        return Auth::user()->projects;
    }


    /**
     * Handle request to save a new Project.
     *
     * @param SaveProjectRequest $request
     * @return Project
     */
    public function postSaveNew(SaveProjectRequest $request)
    {
        $attributes = $request->all();
        $attributes['user_id'] = Auth::id();
        return Project::create($attributes);
    }

    public function getSingleProject(Project $project)
    {
        return $project->load([
            'folders' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'folders.files' => function ($query) {
                $query->orderBy('position', 'asc');
            }
        ]);
    }

    /**
     * Show the view for a single Project.
     *
     * @param Project $project
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getProject(Project $project)
    {

        $project->load([
            'folders' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'folders.files' => function ($query) {
                $query->orderBy('position', 'asc');
            }
        ]);

        return view('projects.single', compact('project'));
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
        $updatedModels = $request->all();

        if ($updatedProject = $updatedModels['project']) {
            $project->update($updatedProject);
        }

        if ($updatedFolders = $updatedModels['folders']) {
            foreach ($updatedFolders as $id => $updatedFolder) {
                if ($id !== $updatedFolder['id']) abort(403, "Tried to update a different folder");
                $projectFolder = ProjectFolder::find($id);
                if(! Gate::allows('updateFolder', [$project, $projectFolder])) abort(403, "Folder doesn't belong to Project");
                $projectFolder->update($updatedFolder);
            }
        }

        if ($updatedFiles = $updatedModels['files']) {
            foreach ($updatedFiles as $id => $updatedFile) {
                if ($id !== $updatedFile['id']) abort(403, "Tried to update a different file");
                $projectFile = ProjectFile::find($id);
                if(! Gate::allows('updateFile', [$project, $projectFile])) abort(403, "File doesn't belong to Project");
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
        $project->delete();
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
        if ($projectFolder->project_id !== $project->id) abort(403, "Folder does not belong to right project");
        $projectFolder->delete();
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
        if ($projectFolder->project_id !== $project->id) abort(403, "Folder does not belong to right project");
        return $projectFolder->files()->create($request->all());
    }


    /**
     * Add Comment to a ProjectFile.
     *
     * @param Project $project
     * @param ProjectFile $projectFile
     * @param AddCommentRequest $request
     * @return Model
     */
    public function postAddComment(Project $project, ProjectFile $projectFile, AddCommentRequest $request)
    {
        if ($projectFile->folder->project_id !== $project->id) abort(403, "File does not belong to project");
        return $projectFile->comments()->create([
            'subject_id' => $projectFile->id,
            'subject_type' => 'App\\ProjectFile',
            'body' => $request->body,
            'user_id' => Auth::id()
        ])->load('sender');
    }

}

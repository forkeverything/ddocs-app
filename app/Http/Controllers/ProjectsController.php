<?php

namespace App\Http\Controllers;

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

class ProjectsController extends Controller
{
    public function __construct()
    {
        // Only authenticated users can access projects
        $this->middleware('auth');

        $this->middleware('can:view,project', [
            'only' => [
                'getProject'
            ]
        ]);

        $this->middleware('can:update,project', [
            'only' => [
                'putUpdate',
                'delete',
                'postCreateFolder',
                'putUpdateFolder',
                'putUpdateFile',
                'deleteFolder',
                'postAddFile'
            ]
        ]);
    }

    /**
     * Get List of all User's projects.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAll()
    {
        // TODO ::: allow to view project's shared by other team members
        $projects = Auth::user()->projects;
        return view('projects.all', compact('projects'));
    }

    /**
     * Handle request to save a new Project.
     *
     * @param SaveProjectRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSaveNew(SaveProjectRequest $request)
    {
        $attributes = $request->all();
        $attributes['user_id'] = Auth::id();
        $project = Project::create($attributes);
        return redirect('/projects/' . $project->id);
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
     * Update a given Project.
     *
     * @param Project $project
     * @param Request $request
     * @return Project
     */
    public function putUpdate(Project $project, Request $request)
    {
        $project->update($request->all());
        return $project;
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
     * Updates Project Folder
     *
     * @param Project $project
     * @param ProjectFolder $projectFolder
     * @param Request $request
     * @return Model
     */
    public function putUpdateFolder(Project $project, ProjectFolder $projectFolder, Request $request)
    {
        if ($projectFolder->project_id !== $project->id) abort(403, "Folder does not belong to right project");
        $projectFolder->update($request->all());
        return $projectFolder;
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
     * Update Project File.
     *
     * @param Project $project
     * @param ProjectFile $projectFile
     * @param Request $request
     * @return ProjectFile
     */
    public function putUpdateFile(Project $project, ProjectFile $projectFile, AddProjectFileRequest $request)
    {
        if($projectFile->folder->project_id !== $project->id) abort(403, "File does not belong to project");
        if(ProjectFolder::findOrFail($request->project_folder_id)->project_id !== $project->id) abort(403, "Trying to put file into folder that doesn't belong to this project.");
        $projectFile->update($request->all());
        return $projectFile;
    }

}

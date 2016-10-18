<?php

namespace App\Http\Controllers;

use App\FileRequest;
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
                'postCreateFolder',
                'postAddFile',
                'putUpdateItems',
                'delete',
                'deleteFolder',
                'postAttachFileRequest',
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

    /**
     * Single Project in JSON
     *
     * @param Project $project
     * @return Model
     */
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
        $this->authorize('updateFolder', [$project, $projectFolder]);
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
        $this->authorize('addFile', [$project, $projectFolder]);
        return $projectFolder->files()->create($request->all());
    }

    /**
     * Attaches a FileRequest to a ProjectFile.
     *
     * @param Project $project
     * @param Request $request
     * @return Model
     */
    public function postAttachFileRequest(Project $project, Request $request)
    {
        $projectFile = ProjectFile::find($request->project_file_id);
        $this->authorize('updateFile', [$project, $projectFile]);
        $fileRequest = FileRequest::findByHash($request->file_request_hash);
        $this->authorize('update', $fileRequest);

        $projectFile->update([
            'file_request_id' => $fileRequest->id
        ]);

        return $projectFile->load('fileRequest');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveProjectRequest;
use App\Project;
use App\ProjectCategory;
use App\ProjectFile;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    public function __construct()
    {
        // Only authenticated users can make projects
        $this->middleware('auth');

        $this->middleware('can:view,project', [
            'only' => ['getSingle']
        ]);

        $this->middleware('can:update,project', [
            'only' => [
                'putUpdate',
                'delete',
                'postNewCategory',
                'postNewFile'
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
     * Show form to start a Project.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getStartForm()
    {
        return view('projects.start');
    }

    /**
     * Show the view for a single Project.
     *
     * @param Project $project
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSingle(Project $project)
    {
        $project->withItems();
        return view('projects.single', compact('project'));
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
     * Add a new Project Category within a Project.
     *
     * @param Project $project
     * @param Request $request
     * @return ProjectCategory
     */
    public function postNewCategory(Project $project, Request $request)
    {
        return ProjectCategory::create($request->all());
    }

    /**
     * Add a new Project File within a Project.
     *
     * @param Project $project
     * @param Request $request
     * @return ProjectFile
     */
    public function postNewFile(Project $project, Request $request)
    {
        return ProjectFile::create($request->all());
    }
}

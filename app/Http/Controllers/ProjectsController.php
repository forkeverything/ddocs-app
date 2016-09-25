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
            'only' => [
                'getSingle',
                'getProjectData'
            ]
        ]);

        $this->middleware('can:update,project', [
            'only' => [
                'putUpdate',
                'delete',
                'postNewCategory',
                'postNewFile',
                'putUpdateItem',
                'putUpdatePositions',
                'deleteItem'
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

    /**
     * Update Single Item fields.
     *
     * @param Project $project
     * @param Request $request
     * @return mixed
     */
    public function putUpdateItem(Project $project, Request $request)
    {
        $item = $this->findProjectItem($request->type, $request->id, $project->id);

        $item->update($request->all());
        return $item;
    }

    /**
     * Update Positions and item heirarchy within Project.
     *
     * @param Project $project
     * @param Request $request
     * @return Project
     */
    public function putUpdatePositions(Project $project, Request $request)
    {
        // track down item
        $targetItem = $this->findProjectItem($request->type, $request->id, $project->id);

        // Move previous siblings positions up

            $currentSiblings = getProjectItems($targetItem->parent_type, $targetItem->parent_id);
            foreach ($currentSiblings as $siblingItem) {
                if($targetItem->position < $siblingItem->position) $this->findProjectItem($siblingItem->type, $siblingItem->id, $project->id)->update(['position' => ($siblingItem->position - 1)]);
            }

        // Move new siblings positions down

            $positionToInsert = $request->position;
            $parentItems = getProjectItems($request->parent_type, $request->parent_id);

            // Make sure our new position is within min(0) and max(number of siblings)
            if($positionToInsert > count($parentItems)) $positionToInsert = count($parentItems);    // insert at end
            if($positionToInsert < 0) $positionToInsert = 0;    // insert at beginning

            // For each direct child item
            foreach ($parentItems as $item) {
                // Move everything down to make way for new item
                if ($item->position >= $positionToInsert) $this->findProjectItem($item->type, $item->id, $project->id)->update(['position' => ($item->position + 1)]);
            }

        // update / insert our new item
        $targetItem->update($request->all());

        return $project->withItems();
    }

    /**
     * Delete a Project's Item
     *
     * @param Project $project
     * @param $type
     * @param $id
     * @return string
     */
    public function deleteItem(Project $project, $type, $id)
    {
        if($type === 'category') $type = 'App\ProjectCategory';
        if($type === 'file') $type = 'App\ProjectFile';
        $targetItem = $this->findProjectItem($type, $id, $project->id);

        // Move lower siblings up
        $parentItems = getProjectItems($targetItem->parent_type, $targetItem->parent_id);
        foreach ($parentItems as $item) if ($item->position > $targetItem->position) $this->findProjectItem($item->type, $item->id, $project->id)->update(['position' => ($item->position - 1)]);

        $targetItem->deleteAllIncludingChildren();

        return $project->withItems();
    }

    /**
     * Track down the Model for a Project Item.
     *
     * @param $type
     * @param $id
     * @param $project_id
     * @return mixed
     */
    protected function findProjectItem($type, $id, $project_id)
    {
        $item = call_user_func($type . '::find', $id);
        if($item->project_id && $item->project_id !== $project_id) abort(403, "Board item does not belong to project");
        return $item;
    }

    /**
     * Returns Project in data (JSON) format.
     *
     * @param Project $project
     * @return $this
     */
    public function getProjectData(Project $project)
    {
        return $project->withItems();
    }
}

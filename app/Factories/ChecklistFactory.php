<?php


namespace App\Factories;


use App\Checklist;
use App\File;
use App\Http\Requests\NewChecklistRequest;
use App\User;

class ChecklistFactory
{

    /**
     * The Checklist model
     * @var
     */
    protected $checklist;

    /**
     * The request to make a new Checklist.
     *
     * @var NewChecklistRequest
     */
    protected $request;

    /**
     * The User making the Checklist
     *
     * @var User
     */
    protected $user;

    /**
     * ChecklistFactory constructor.
     *
     * @param NewChecklistRequest $request
     * @param User $user
     */
    public function __construct(NewChecklistRequest $request, User $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * Static wrapper...
     *
     * @param NewChecklistRequest $request
     * @param User $user
     * @return static
     */
    public static function make(NewChecklistRequest $request, User $user)
    {
        $factory = new static($request, $user);
        $factory->createChecklist()
                ->createFiles();

        return $factory->checklist;
    }

    /**
     * Create our Checklist model.
     */
    protected function createChecklist()
    {
        $this->checklist = Checklist::create([
            'recipient' => $this->request->recipient,
            'name' => $this->request->name,
            'description' => $this->request->description,
            'user_id' => $this->user->id
        ]);

        return $this;
    }

    /**
     * Create each individual File model from the request.
     */
    protected function createFiles()
    {
        foreach ($this->request->requested_files as $file) {
            $this->checklist->files()->create([
                'name' => $file['name'],
                'description' => $file['description'],
                'due' => $file['due'],
                'required' => $file['required']
            ]);
        }

        return $this;
    }
}
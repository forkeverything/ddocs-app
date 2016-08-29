<?php


namespace App\Factories;


use App\Checklist;
use App\Events\ChecklistCreated;

use App\Http\Requests\NewChecklistRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class ChecklistFactory
{

    /**
     * The Checklist model
     *
     * @var Checklist
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
     * @return Checklist
     */
    public static function make(NewChecklistRequest $request, User $user)
    {
        $factory = new static($request, $user);
        $factory->processPayment()
                ->createChecklist()
                ->createFiles();

        return $factory->checklist;
    }


    /**
     * Receives request from email webhook to create a checklist via cc. Parse
     * the email parameters and manually create NewChecklistRequest Class.
     * The actually heavy-lifting is still diverted to make().
     *
     * @param Request $request
     * @param User $user
     * @return Checklist
     */
    public static function makeFromEmail(Request $request, User $user)
    {
        // Turn request into array - because it's in camel case
        $request = $request->all();

        $body = $request["TextBody"];

        preg_match_all("/^-\s*([^\[\n]*)(\[(.*)\])?/m", $body, $matches);

        $requestedFiles = [];

        foreach ($matches[1] as $key => $fileName) {
            $file = [
                "name" => $fileName,
                "description" => null,
                "due" => isset($matches[3][$key]) ? $matches[3][$key] : null,
                "required" => 1
            ];
            array_push($requestedFiles, $file);
        }

        // Build our form request manually
        $newChecklistRequest = new NewChecklistRequest([
            'recipient' => $request["ToFull"][0]["Email"],
            'name' => $request["Subject"],
            'description' => null,
            'requested_files' => $requestedFiles
        ]);

        return self::make($newChecklistRequest, $user);
    }

    /**
     * Process the payment required to create a checklist.
     *
     * @return $this
     */
    protected function processPayment()
    {
        // If user is subscribed, we'll just skip credit deduction
        if ($this->user->subscribed('main')) return $this;

        $this->user->minusOneCredit();
        return $this;
    }

    /**
     * Create our Checklist model.
     *
     * @return $this
     */
    protected function createChecklist()
    {
        $this->checklist = Checklist::create([
            'recipient' => $this->request->recipient,
            'name' => $this->request->name,
            'description' => $this->request->description,
            'user_id' => $this->user->id
        ]);

        Event::fire(new ChecklistCreated($this->checklist));

        return $this;
    }

    /**
     * Create each individual File model from the request.
     */
    protected function createFiles()
    {
        foreach ($this->request->requested_files as $file) {
            $this->checklist->requestedFiles()->create([
                'name' => $file['name'],
                'description' => $file['description'],
                'due' => $file['due'],
                'required' => $file['required']
            ]);
        }

        return $this;
    }

}
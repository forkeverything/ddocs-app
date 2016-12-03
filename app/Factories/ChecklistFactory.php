<?php


namespace App\Factories;


use App\Checklist;
use App\Events\ChecklistCreated;

use App\File;
use App\Http\Requests\NewChecklistRequest;
use App\Notifications\NewChecklistNotification;
use App\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Mail;

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
    public function __construct(Request $request, User $user)
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
                ->createRecipients()
                ->createFiles()
                ->fireChecklistCreatedEvent();

        return $factory->checklist;
    }

    /**
     * Receives request from email webhook to create a checklist via cc. Parse
     * the email parameters and manually create NewChecklistRequest Class.
     * The actually heavy-lifting is still diverted to make().
     *
     * @param Request $request
     * @param User $user
     * @return Checklist|void
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
                "due" => isset($matches[3][$key]) ? $matches[3][$key] : null
            ];
            array_push($requestedFiles, $file);
        }

        $recipients = [];

        // add TO address
        array_push($recipients, $request["ToFull"][0]["Email"]);

        // add each CC: that is NOT inbound email
        foreach ($request["CcFull"] as $cc) {
            $email = $cc["Email"];
            if ($email !== env('MAIL_CREATE_CHECKLIST_ADDRESS')) array_push($recipients, $email);
        }

        // Remove inbound email address to avoid loop
        $index = array_search(env('MAIL_CREATE_CHECKLIST_ADDRESS'), $recipients);
        if ($index !== false) unset($recipients[$index]);
        if (!count($recipients) > 0) return;

        // Build our form request manually
        $newChecklistRequest = new NewChecklistRequest([
            'recipients' => $recipients,
            'name' => $request["Subject"],
            'description' => null,
            'requested_files' => $requestedFiles
        ]);

        return self::make($newChecklistRequest, $user);
    }

    /**
     * Update the recipients list for a Checklist.
     *
     * @param Checklist $checklist
     * @param $newRecipients
     * @return \App\Recipient[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function updateRecipients(Checklist $checklist, $newRecipients)
    {
        $oldRecipients = $checklist->recipients;
        // Delete recipients that have been removed...
        foreach ($oldRecipients as $recipient) {
            if (!in_array($recipient->email, $newRecipients)) $recipient->delete();
        }
        // Add new recipients
        foreach ($newRecipients as $recipientEmail) {
            if (!in_array($recipientEmail, $oldRecipients->pluck('email')->toArray())) {
                $attr = [
                    'email' => $recipientEmail
                ];
                // If recipient has an account we'll link it here
                if($existingUser = User::findByEmail($recipientEmail)) $attr["user_id"] = $existingUser->id;
                $recipient = $checklist->recipients()->create($attr);
                $target = $recipient->user_id ? $existingUser : $recipient;
                $target->notify(new NewChecklistNotification($checklist, $recipient));
            }
        }
        return $checklist->fresh('recipients')->recipients;
    }

    /**
     * Adds a new file request to a given Checklist.
     *
     * @param Checklist $checklist
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function addFileToExistingChecklist(Checklist $checklist, Request $request)
    {
        $factory = new static($request, $checklist->user);

        // Track down or create new File
        if (!$fileModel = $factory->findFileModel($request->name, $factory->user->id)) {
            $fileModel = $factory->createFileModel($request->name, $request->description, $factory->user->id);
        };

        // Create file request
        return $checklist->requestedFiles()->create([
            'due' => $request->due,
            'file_id' => $fileModel->id
        ]);
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

        // CREDIT DEDUCTION - THE ONLY PLACE WHERE THIS OCCURS
//        $this->user->minusOneCredit();
        // TODO ::: Reactivate credit deduction when out of beta.

        return $this;
    }

    /**
     * Create our Checklist model.
     *
     * @return $this
     */
    protected function createChecklist()
    {
        $password = $this->request->password ? Hash::make($this->request->password) : null;
        $this->checklist = Checklist::create([
            'name' => $this->request->name,
            'description' => $this->request->description,
            'user_id' => $this->user->id,
            'password' => $password
        ]);

        return $this;
    }

    /**
     * Create Recipient models from request.
     *
     * @return $this
     */
    protected function createRecipients()
    {
        foreach ($this->request->recipients as $recipient) {
            $this->checklist->recipients()->create([
                'email' => $recipient,
            ]);
        }
        return $this;
    }

    /**
     * Create File model that file requests reference to.
     *
     * @param $name
     * @param $description
     * @param $userId
     * @return File
     */
    protected function createFileModel($name, $description, $userId)
    {
        return File::create([
            'name' => $name,
            'description' => $description,
            'user_id' => $userId
        ]);
    }

    /**
     * Try to track down a File model by name and user.
     *
     * @param $name
     * @param $userId
     * @return File
     */
    protected function findFileModel($name, $userId)
    {
        return File::where('name', $name)->where('user_id', $userId)->first();
    }

    /**
     * Create each individual File model from the request.
     */
    protected function createFiles()
    {
        // Clean array of duplicate values (case insensitive)
        foreach ($this->request->requested_files as $file) {

            // Track down or create new File
            if (!$fileModel = $this->findFileModel($file['name'], $this->user->id)) {
                $fileModel = $this->createFileModel($file['name'], $file['description'], $this->user->id);
            };

            // Create a request for the file
            $this->checklist->requestedFiles()->create([
                'due' => $file['due'],
                'file_id' => $fileModel->id
            ]);
        }

        return $this;
    }

    /**
     * Fire when complete.
     *
     * @return $this
     */
    protected function fireChecklistCreatedEvent()
    {
        Event::fire(new ChecklistCreated($this->checklist));
        return $this;
    }

}
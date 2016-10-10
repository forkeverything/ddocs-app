<?php


namespace App\Repositories;


use App\Checklist;
use App\File;
use App\FileRequest;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FileRequestsRepository extends EloquentRepository
{
    /**
     * Sortable fields for FileRequest(s)
     *
     * @var array
     */
    protected $sortableFields = [
        'name',
        'version',
        'due',
        'status'
    ];

    /**
     * Searchable fields for FileRequest(s)
     *
     * @var array
     */
    protected $searchableFields = [
        'name'
    ];

    /**
     * FileRequestsRepository constructor. Set up our query builder here.
     *
     * @param Model $model
     * @internal param Checklist $checklist
     */
    public function __construct(Model $model)
    {
        $this->query = FileRequest::join('files', 'file_requests.file_id', '=', 'files.id')
                                  ->selectRaw('
                                      file_requests.*, 
                                      files.name, 
                                      files.description
                                    ');

        // If we're looking for requests for a specific Checklist or User
        if ($model instanceof Checklist) {
            $this->query->where('checklist_id', $model->id);
        } elseif ($model instanceof User) {
            $this->query->whereIn('checklist_id', $model->checklists->pluck('id'));
        }
    }

    /**
     * Getting requests belonging to single Checklist.
     *
     * @param Checklist $checklist
     * @return static
     */
    public static function forChecklist(Checklist $checklist)
    {
        return new static($checklist);
    }

    /**
     * Checklists belonging to any Checklist that belongs to single User.
     *
     * @param User $user
     * @return static
     * @internal param Checklist $checklist
     */
    public static function forUser(User $user)
    {
        return new static($user);
    }


    /**
     * Status filter.
     *
     * @param $status
     * @return $this
     */
    public function withStatus($status)
    {
        $possibleStatuses = [
            'waiting', 'received', 'rejected'
        ];

        if (in_array($status, $possibleStatuses, true)) $this->{'status'} = $status;
        if (isset($this->status)) $this->query->where('status', $this->status);
        return $this;
    }

    /**
     * Append the number of received files that match the
     * search query.
     *
     * @return $this
     */
    public function withNumReceivedFiles()
    {
        // Copy & run query
        $duplicatedQuery = clone $this->query;
        $numReceivedFiles = $duplicatedQuery->where('status', 'received')
                                            ->select(DB::raw(1))
                                            ->get()
                                            ->count();
        // Append to object
        $this->{'num_received_files'} = $numReceivedFiles;

        return $this;
    }

    /**
     * Search Checklist Names as well as Recipient Emails.
     *
     * @param $searchTerm
     * @return $this
     */
    public function searchChecklistNamesAndRecipientEmails($searchTerm)
    {
        $this->query->orWhereExists(function ($q) use ($searchTerm) {
            $q->select(DB::raw(1))
              ->from('checklists')
              ->join('recipients', 'recipients.checklist_id', '=', 'checklists.id')
              ->whereRaw('file_requests.checklist_id = checklists.id')              // Only interested in checklists that our current set of FileRequests belong to
              ->where('checklists.name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('recipients.email', 'LIKE', "%{$searchTerm}%");
        });

        return $this;
    }

}
<?php


namespace App\Repositories;


use App\Checklist;
use App\File;
use App\FileRequest;
use Illuminate\Support\Facades\DB;

class FilesRequestsRepository extends EloquentRepository
{
    /**
     * Sortable fields for File(s)
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
     * Searchable fields for File(s)
     *
     * @var array
     */
    protected $searchableFields = [
        'name'
    ];

    /**
     * FilesRequestsRepository constructor. Set up our query builder here.
     *
     * @param Checklist $checklist
     */
    public function __construct(Checklist $checklist)
    {
        $this->query = FileRequest::where('checklist_id', $checklist->id);
    }

    /**
     * Static wrapper to build our Repo.
     *
     * @param Checklist $checklist
     * @return static
     */
    public static function forChecklist(Checklist $checklist)
    {
        return new static($checklist);
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


}
<?php


namespace App\Repositories;


use App\Checklist;
use App\File;
use App\FileRequest;

class FilesRequestsRepository extends EloquentRepository
{
    /**
     * Sortable fields for File(s)
     *
     * @var array
     */
    protected $sortableFields = [
        'required',
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
     * Required / Optional filter.
     *
     * @param $required
     * @return $this
     */
    public function whereRequired($required)
    {
        $possibleRequirements = [
            "1", "0"
        ];
        if(in_array($required, $possibleRequirements, true)) $this->{'required'} = (int)$required;
        if (isset($this->required)) $this->query->where('required', $this->required);
        return $this;
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

        if(in_array($status, $possibleStatuses, true)) $this->{'status'} = $status;
        if(isset($this->status)) $this->query->where('status', $this->status);
        return $this;
    }


}
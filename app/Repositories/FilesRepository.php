<?php


namespace App\Repositories;


use App\Checklist;
use App\File;

class FilesRepository extends EloquentRepository
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
     * FilesRepository constructor. Set up our query builder here.
     *
     * @param Checklist $checklist
     */
    public function __construct(Checklist $checklist)
    {
        $this->query = File::where('checklist_id', $checklist->id);
    }

    public static function forChecklist(Checklist $checklist)
    {
        return new static($checklist);
    }


}
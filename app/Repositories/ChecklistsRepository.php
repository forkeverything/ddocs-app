<?php


namespace App\Repositories;


use App\Checklist;

class ChecklistsRepository
{

    /**
     * Hold our Checklist Model
     * @var
     */
    protected $checklist;

    protected $query;

    public function __construct(Checklist $checklist)
    {
        dd($checklist);
        $this->checklist = $checklist;
    }

    public static function find($id)
    {
        $checklist = Checklist::findOrFail($id);
        $repository = new static($checklist);
        return $repository;
    }

}
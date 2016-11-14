<?php


namespace App\Repositories;


use App\Checklist;
use App\User;
use DB;

class ChecklistsRespository extends EloquentRepository
{
    /**
     * Which fields are sortable/
     *
     * @var array
     */
    protected $sortableFields = [
        'name',
        'created_at'
    ];

    /**
     * Which fields to include in our text search.
     *
     * @var array
     */
    protected $searchableFields = [
        'name'
    ];


    /**
     * Build our repo.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->query = Checklist::where('user_id', $user->id);
    }

    /**
     * Static wrapper - nothing new here!
     *
     * @param User $user
     * @return static
     */
    public static function forUser(User $user)
    {
        return new static($user);
    }

    /**
     * Perform search by Recipient emails too.
     *
     * @param $searchTerm
     * @return $this
     */
    public function searchWithRecipients($searchTerm)
    {
        $this->query->where(function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhereExists(function ($subQuery) use ($searchTerm){
                  $subQuery->select(DB::raw(1))
                    ->from('recipients')
                    ->whereRaw('recipients.checklist_id = checklists.id')
                    ->where('recipients.email', 'LIKE', "%{$searchTerm}%");
              });
        });

        return $this;
    }


}
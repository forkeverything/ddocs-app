<?php


namespace App\Repositories;


use App\Checklist;
use App\User;

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


}
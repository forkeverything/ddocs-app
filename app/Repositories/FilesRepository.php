<?php


namespace App\Repositories;


use App\File;
use App\User;

class FilesRepository extends EloquentRepository
{


    /**
     * Which fields are sortable/
     *
     * @var array
     */
    protected $sortableFields = [
        'name'
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
     * Build Repo

     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->query = File::where('user_id', $user->id);
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
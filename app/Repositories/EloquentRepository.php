<?php


namespace App\Repositories;


use App\Utilities\Traits\EloquentIntegerAndDateFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

abstract class EloquentRepository
{

    use EloquentIntegerAndDateFilters;

    /**
     * Query Builder Class.
     *
     * @var
     */
    protected $query;

    /**
     * Model fields that are sortable. The
     * first given field will be the
     * default sort
     *
     * @var
     */
    protected $sortableFields = [];

    /**
     * Model fields that we can perform searches on, Accepts
     *  a string to search relationship table fields:
     * 'parent_table_name.child_table_name.child_table_column'
     * @var array
     */
    protected $searchableFields = [];

    /**
     * Holds the parameters used to fetch the results.
     *
     * @var
     */
    protected $queryParameters;

    /**
     * Wrapper for method on Query Builder that lets
     * us eager load our database relationships.
     *
     * @return $this
     */
    public function with()
    {
        $arg = func_get_args()[0];
        if (is_string($arg) || is_array($arg)) $this->query->with($arg);
        return $this;
    }

    /**
     * Apply a sort to the Purchase Requests
     *
     * @param null $sort
     * @param null $order
     * @return $this
     */
    public function sortOn($sort = null, $order = null)
    {
        $this->{'order'} = ($order === 'desc') ? 'desc' : 'asc';
        $this->{'sort'} = in_array($sort, $this->sortableFields) ? $sort : $this->sortableFields[0];
        $this->query->orderBy($this->sort, $this->order);
        return $this;
    }

    /**
     * Sort that puts null values last.
     *
     * @param $sort
     * @param $order
     * @param $nullableFields
     * @return $this
     */
    public function sortWithNull($sort, $order, $nullableFields)
    {
        $this->{'order'} = ($order === 'desc') ? 'desc' : 'asc';
        $this->{'sort'} = in_array($sort, $this->sortableFields) ? $sort : $this->sortableFields[0];

        if( in_array($sort, $nullableFields) && $order === 'asc') {
            $sort = '-' . $sort;
            $this->query->orderBy(\DB::raw($sort), 'desc');
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }

        return $this;
    }

    /**
     * Wrapper - Used to only select certain
     * fields
     *
     * @param $fields
     * @return $this
     */
    public function select($fields)
    {
        $this->query->select($fields);
        return $this;
    }

    /**
     * Wrapper - just in case we don't
     * want to paginate and just retrieve it
     * in one go
     *
     * @return mixed
     */
    public function get()
    {
        $results = [
            'data' => $this->query->get()
        ];
        return $this->addPropertiesToResults($results);
    }

    /**
     * Wrapper - for having() on Query Builder. having() can be used
     * on aggregates (SUM, COUNT, etc...) -- WHERE cannot be used.
     *
     * @param $column
     * @param $operator
     * @param $value
     * @return $this
     */
    public function having($column, $operator, $value)
    {
        $this->query->having($column, $operator, $value);
        return $this;
    }

    /**
     * Search function that searches a target table's fields
     * as well as any directly related tables
     *
     * @param $term
     * @return $this
     */
    public function searchFor($term, $searchFieldsArray = null)
    {
        if ($term) {
            $this->{'search'} = $term;
            // If one-time search fields defined, use them
            if ($searchFieldsArray) $this->searchableFields = $searchFieldsArray;
            // perform search
            $this->query->where(function ($query) use ($term) {
                foreach ($this->searchableFields as $index => $field) {
                    $fieldsArray = explode('.', $field);

                    if (count($fieldsArray) === 1) {
                        $this->searchQueryDirect($query, $fieldsArray[0], $term, $index);
                    } else {
                        $this->searchQueryRelated($query, $fieldsArray, $term, $index);
                    }
                }
                return $query;

            });
        }
        return $this;
    }

    /**
     * Performs a search query on a field directly related
     * to the Model
     *
     * @param Builder $query
     * @param $field
     * @param $term
     * @param $index
     * @return mixed
     */
    protected function searchQueryDirect(Builder $query, $field, $term, $index)
    {
        $funcName = $index === 0 ? 'where' : 'orWhere';
        return call_user_func([$query, $funcName], $field, 'LIKE', '%' . $term . '%');
    }

    /**
     * Performs search on field that is on a related table. As
     * defined in the $searchableFields[] on the model.
     *
     * @param Builder $query
     * @param $fieldArray
     * @param $term
     * @param $index
     * @return mixed
     */
    protected function searchQueryRelated(Builder $query, $fieldArray, $term, $index)
    {
        $funcName = $index === 0 ? 'whereExists' : 'orWhereExists';
        $callback = function ($q) use ($fieldArray, $term) {
            $q->select(DB::raw(1))
              ->from(($fieldArray[2]))
              ->whereRaw($fieldArray[0] . '.' . $fieldArray[1] . '=' . $fieldArray[2] . '.id')
              ->where($fieldArray[3], 'LIKE', '%' . $term . '%');
        };
        return call_user_func([$query, $funcName], $callback);
    }


    /**
     * Attaches this object's properties
     * to the results object that is
     * returned to Client
     *
     * @param $object
     */
    protected function addPropertiesToResults($array)
    {
        // Transfer object properties onto it
        foreach (get_object_vars($this) as $key => $value) {
            if (!($value instanceof LengthAwarePaginator) && !($value instanceof Builder)) {
                if ($key !== 'sortableFields' && $key !== 'searchableFields' && $key !== 'queryParameters') $this->queryParameters[$key] = $value;
            }
        }
        $array['query_parameters'] = $this->queryParameters;
        return $array;
    }

    /**
     * Finally: Fetch Results and paginate it
     * by set number of items per Page
     * (untested)
     * @param $itemsPerPage
     * @return $this
     */
    public function paginate($itemsPerPage = 20)
    {
        // Set paginated property to hold our paginated results
        $paginatedArray = $this->query->paginate($itemsPerPage)->toArray();
        // add our custom properties
        return $this->addPropertiesToResults($paginatedArray);
    }

    /**
     * Wrapper - Method on Query Builder that removes
     * duplicates from retrieved results set.
     *
     * @return $this
     */
    public function distinct()
    {
        $this->query->distinct();
        return $this;
    }

    /**
     * Wrapper - limit number of results for the
     * query
     *
     * @param $limit
     * @return $this
     */
    public function take($limit = null)
    {
        if ($limit) $this->query->take($limit);
        return $this;
    }

    /**
     * Just a get() wrapper for the Query Builder. This is
     * used for testing because we don't need to know the
     * Query Properties used (for client).
     *
     * @return mixed
     */
    public function getWithoutQueryProperties()
    {
        return $this->query->get();
    }
}
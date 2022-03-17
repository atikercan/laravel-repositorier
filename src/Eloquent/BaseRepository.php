<?php

namespace Atikercan\LaravelRepositorier\Eloquent;

use Atikercan\LaravelRepositorier\Interfaces\BaseRepositoryInterface;
use Atikercan\LaravelRepositorier\Interfaces\RepositoryInterface;
use Atikercan\LaravelRepositorier\Criteria;
use Atikercan\LaravelRepositorier\Scope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements BaseRepositoryInterface {
    /**
     * Variable to keep pre populated fields to apply to every single object
     * @var array
     */
    protected array $prePopulatedFields = [];
    /**
     * Model object to use in the repository
     */
    protected $model = null;

    /**
     * relations to load automatically
     */
    protected array $with = [];

    /**
     * Variable to keep criteria to be used in queries
     * @var null|Criteria
     */
    protected ?Criteria $criteria = null;

    /**
     * Variable to keep scopes to be used in queries
     * @var null|Scope
     */
    protected ?Scope $scope = null;

    /**
     * Query to use in database actions
     */
    protected $query = null;

    public function __construct()
    {
        if ($this->model == null) {
            throw new Exception("Model class is not defined!");
        }
        $this->resetQuery();
    }

    /**
     * Fin an item by id
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->model::find($id);
    }

    /**
     * Return all items
     * @return Collection|null
     */
    public function all(): ?Collection
    {
        $query = $this->query()->clone();
        if(!is_null($this->criteria)) {
            $this->criteria->apply($query);
        }
        $this->resetQuery();

        return $query->all();
    }

    /**
     * Returns the first matching row
     * @return mixed|null
     */
    public function first(): mixed
    {
        $query = $this->query()->clone();
        if(!is_null($this->criteria)) {
            $this->criteria->apply($query);
        }
        $this->resetQuery();

        return $query->first();
    }

    /**
     * Return query builder instance to perform more manouvers
     * @return Builder|null
     */
    public function query(): ?Builder
    {
        return $this->query;
    }

    /**
     * Resets query builder and returns it
     * @return Builder|null
     */
    public function resetQuery(): ?Builder
    {
        $query = $this->model::query();

        //applyScopes
        if(!is_null($this->scope)) {
            $this->scope->apply($query);
        }

        $this->query = $query;

        return $this->query;
    }

    /**
     * Create an item
     * @param array|mixed $data
     * @return Model|null
     */
    public function create($data): ?Model
    {
        $data = array_merge($data, $this->getPrePopulatedFields());
        return $this->model::create($data);
    }

    /**
     * Update a model
     * @param int|mixed $idOrModel
     * @param array|mixed $data
     * @return bool|mixed
     */
    public function update($idOrModel, array $data)
    {
        $data = array_merge($data, $this->getPrePopulatedFields());
        if( is_numeric($idOrModel) ) {
            return $this->model::find($idOrModel)->update($data);
        } else {
            return $idOrModel->update($data);
        }
    }

    /**
     * Delete a model
     * @param int|Model $id
     */
    public function delete($id)
    {
        $pk = (new $this->model)->getKeyName();
        $query = $this->resetQuery()->clone();
        return $query->where($pk, $id)->delete();
    }

    /**
     * Paginates all matched items
     * @param int $limit
     * @return LengthAwarePaginator|null
     */
    public function paginate(int $limit = 15) : LengthAwarePaginator {
        $query = $this->resetQuery()->clone();
        if(!is_null($this->criteria)) {
            $this->criteria->apply($query);
        }
        $this->resetQuery();
        return $query->paginate($limit);
    }

    /**
     * @return Criteria|null
     */
    public function getCriteria(): ?Criteria
    {
        return $this->criteria;
    }

    /**
     * @param Criteria|null $criteria
     * @return BaseRepositoryInterface
     */
    public function setCriteria(?Criteria $criteria): BaseRepositoryInterface
    {
        $this->criteria = $criteria;
        return $this;
    }

    /**
     * @return Scope|null
     */
    public function getScope(): ?Scope
    {
        return $this->scope;
    }

    /**
     * @param Scope|null $scope
     * @return BaseRepositoryInterface
     */
    public function setScope(?Scope $scope): BaseRepositoryInterface
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return array
     */
    public function getPrePopulatedFields(): array
    {
        return $this->prePopulatedFields;
    }

    /**
     * @param array $fields
     * @return BaseRepositoryInterface
     */
    public function setPrepopulatedFields(array $fields): BaseRepositoryInterface
    {
        $this->prePopulatedFields = $fields;
        return $this;
    }



    /**
     * @return array
     */
    public function getWith(): array
    {
        return $this->with;
    }

    /**
     * @param array $with
     */
    public function setWith(array $with): void
    {
        $this->with = $with;
    }
}
